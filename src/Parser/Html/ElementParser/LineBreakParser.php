<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\ElementParser;

use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\AtomMarker;
use Becklyn\Mobiledoc\Parser\Html\HtmlNodeParser;
use Becklyn\Mobiledoc\Parser\Html\Node\ElementNode;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;

class LineBreakParser implements ElementParserInterface
{
    /**
     * @inheritDoc
     */
    public function parse (HtmlNode $node, HtmlNodeParser $nodeParser) : array
    {
        return [
            new AtomMarker("br", ""),
        ];
    }


    /**
     * @inheritDoc
     */
    public function supports (HtmlNode $node) : bool
    {
        return $node instanceof ElementNode && "br" === $node->getTagName();
    }
}
