<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\ElementParser;

use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\TextMarker;
use Becklyn\Mobiledoc\Parser\Html\HtmlNodeParser;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;
use Becklyn\Mobiledoc\Parser\Html\Node\TextNode;

/**
 * Parses text nodes
 */
class TextParser implements ElementParserInterface
{
    /**
     * @inheritDoc
     *
     * @param TextNode $node
     */
    public function parse (HtmlNode $node, HtmlNodeParser $nodeParser) : array
    {
        return [
            new TextMarker($node->getText()),
        ];
    }


    /**
     * @inheritDoc
     */
    public function supports (HtmlNode $node) : bool
    {
        return $node instanceof TextNode;
    }

}
