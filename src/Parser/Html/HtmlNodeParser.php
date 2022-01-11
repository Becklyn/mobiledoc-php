<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html;

use Becklyn\Mobiledoc\Exception\ParseException;
use Becklyn\Mobiledoc\Mobiledoc\Structure\ContentElement;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;
use Becklyn\Mobiledoc\Parser\Html\ElementParser\ElementParserInterface;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;

class HtmlNodeParser
{
    /** @var ElementParserInterface[] */
    private array $parsers = [];


    public function __construct (array $parsers)
    {
        foreach ($parsers as $parser)
        {
            $this->registerParser($parser);
        }
    }


    private function registerParser (ElementParserInterface $parser) : void
    {
        $this->parsers[] = $parser;
    }


    /**
     * @return ContentElement[]
     */
    public function parse (HtmlNode $node) : array
    {
        foreach ($this->parsers as $parser)
        {
            if ($parser->supports($node))
            {
                return $parser->parse($node, $this);
            }
        }

        throw new ParseException(\sprintf("Can't parse node of type: %s", $node->getDebugLabel()), $node);
    }


    /**
     * @param HtmlNode|null $context optional: the context of the node
     *
     * @return Marker[]
     */
    public function parseInline (HtmlNode $node, ?HtmlNode $context = null) : array
    {
        $result = $this->parse($node);

        foreach ($result as $element)
        {
            if (!$element instanceof Marker)
            {
                throw new ParseException(\sprintf(
                    "Can't parse node inline, as a content element of type '%s' was found inside of: %s",
                    \get_class($element),
                    $node->getDebugLabel()
                ), $context);
            }
        }

        /** @var Marker[] $result */

        return $result;
    }
}
