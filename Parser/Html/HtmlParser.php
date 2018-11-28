<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html;

use Becklyn\Mobiledoc\Exception\ParseException;
use Becklyn\Mobiledoc\Mobiledoc\Document;
use Becklyn\Mobiledoc\Mobiledoc\DocumentSerializer;
use Becklyn\Mobiledoc\Mobiledoc\Structure\ContentElement;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\TextMarker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\Section;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\BlockParser;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\ElementParser;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\InlineParser;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\LineBreakParser;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\LinkParser;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;
use Becklyn\Mobiledoc\Parser\Html\Node\NodeTraverser;
use Becklyn\Mobiledoc\Parser\ParseLogger;
use Becklyn\Mobiledoc\Parser\ParseResult;
use Masterminds\HTML5;


class HtmlParser
{
    /**
     * @var \DOMDocument
     */
    private $domDocument;

    /**
     * @var Document
     */
    private $mobiledoc;

    /**
     * @var ParseLogger
     */
    private $logger;

    /**
     * @var ElementParser[]
     */
    private $elementParsers = [];


    /**
     * @var bool
     */
    private $failed = false;


    /**
     * @param string $html
     */
    public function __construct (string $html)
    {
        $this->mobiledoc = new Document();
        $this->logger = new ParseLogger();

        $this->elementParsers = [
            new InlineParser(),
            new BlockParser(),
            new LineBreakParser(),
            new LinkParser(),
        ];

        $html5 = new HTML5();
        $this->domDocument = $html5->parse(\trim($html));
        $root = $this->domDocument->getElementsByTagName("html")[0] ?? null;

        try
        {
            if (null !== $root || !$root instanceof \DOMElement)
            {
                $this->parseRoot($root);
            }
            else
            {
                $this->failed = true;
                $this->logger->log("No root node found.");
            }
        }
        catch (ParseException $exception)
        {
            $this->failed = true;
            $this->logger->log("Parsing failed due to exception: %s", $exception->getMessage());
        }
    }


    /**
     * Parses the root node
     *
     * @param \DOMElement $root
     */
    private function parseRoot (\DOMElement $root) : void
    {
        $this->parseTopLevel(NodeTraverser::getSanitizedChildren($root));
    }


    /**
     * Parses the list of top level nodes
     *
     * @param HtmlNode[] $nodes
     */
    private function parseTopLevel (array $nodes)
    {
        foreach ($nodes as $node)
        {
            $contentElements = null;

            // parse node
            foreach ($this->elementParsers as $parser)
            {
                if ($parser->supports($node))
                {
                    $contentElements = $parser->parse($node);
                    break;
                }
            }

            // if the element is still null, no parser supports this type of node
            if (null === $contentElements)
            {
                $this->logger->log("Encountered unparsable HTML node '%s'", $node->getDebugLabel());
                continue;
            }

            // append parsed elements to document
            foreach ($contentElements as $contentElement)
            {
                if ($contentElement instanceof Marker)
                {
                    $this->mobiledoc->appendToLastParagraph($contentElement);
                }
                else if ($contentElement instanceof Section)
                {
                    $this->mobiledoc->appendSection($contentElement);
                }
                else
                {
                    $this->logger->log("Found invalid content element of type: '%s'.", \get_class($contentElement));
                }
            }
        }
    }


    /**
     * @return ParseResult
     */
    public function getResult () : ParseResult
    {
        if ($this->failed)
        {
            return new ParseResult(null, $this->logger->getMessages());
        }

        $serializer = new DocumentSerializer($this->mobiledoc);
        return new ParseResult($serializer->serialize(), $this->logger->getMessages());
    }
}
