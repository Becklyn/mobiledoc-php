<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html;

use Becklyn\Mobiledoc\Exception\ParseException;
use Becklyn\Mobiledoc\Mobiledoc\Document;
use Becklyn\Mobiledoc\Mobiledoc\DocumentSerializer;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\Section;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\BlockParser;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\DivParser;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\InlineParser;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\LineBreakParser;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\ListParser;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\SpanParser;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\TextParser;
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
     * @var HtmlNodeParser
     */
    private $nodeParser;


    /**
     * @var bool
     */
    private $failed = false;


    /**
     * @param string           $html
     * @param HtmlNodeParser[] $additionalParsers
     * @param bool             $verboseErrors
     */
    public function __construct (string $html, array $additionalParsers = [], bool $verboseErrors = false)
    {
        // region Prepare Instance Variables
        $this->mobiledoc = new Document();
        $this->logger = new ParseLogger();
        // endregion

        // region Prepare HTML
        $html = \trim($html);
        // wrap text in body tags, otherwise the DOM parser has issues with plain texts.
        $html = "<body>{$html}</body>";
        // endregion

        // region Element Parsers
        \array_unshift(
            $additionalParsers,
            new TextParser(),
            new InlineParser(),
            new BlockParser(),
            new LineBreakParser(),
            new SpanParser(),
            new ListParser(),
            new DivParser()
        );

        $this->nodeParser = new HtmlNodeParser($additionalParsers);
        // endregion

        // region Prepare HTML Element
        if (\class_exists(HTML5::class))
        {
            $html5 = new HTML5();
            $this->domDocument = $html5->parse($html);
        }
        else
        {
            $this->domDocument = new \DOMDocument("1.0", "utf-8");
            @$this->domDocument->loadHTML($html);
        }

        $root = $this->domDocument->getElementsByTagName("body")[0];
        // endregion

        // region Parse
        try
        {
            if (null !== $root && $root instanceof \DOMElement)
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
            $this->logger->log(
                "Parsing failed due to exception: %s",
                $verboseErrors ? $exception->getFullMessage() : $exception->getMessage()
            );
        }
        // endregion
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
            // parse node
            $contentElements = $this->nodeParser->parse($node);

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

        // only serialize the non-empty sections, as they don't add anything to the HTML
        $serializer = new DocumentSerializer($this->mobiledoc->getNonEmptySections());
        return new ParseResult($serializer->serialize(), $this->logger->getMessages());
    }
}
