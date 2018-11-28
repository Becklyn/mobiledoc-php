<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\ElementParser;

use Becklyn\Mobiledoc\Exception\ParseException;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\Section;
use Becklyn\Mobiledoc\Parser\Html\HtmlNodeParser;
use Becklyn\Mobiledoc\Parser\Html\Node\ElementNode;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;


class DivParser implements ElementParserInterface
{
    /**
     * @inheritDoc
     *
     * @param ElementNode $node
     */
    public function parse (HtmlNode $node, HtmlNodeParser $nodeParser) : array
    {
        $children = [];
        $hasSections = false;
        $hasMarkers = false;

        foreach ($node->getChildren() as $child)
        {
            foreach ($nodeParser->parse($child) as $nestedElement)
            {
                if ($nestedElement instanceof Section)
                {
                    $hasSections = true;
                }

                if ($nestedElement instanceof Marker)
                {
                    $hasMarkers = true;
                }

                $children[] = $nestedElement;
            }
        }

        if ($hasSections && $hasMarkers)
        {
            throw new ParseException("Can't parse <div> with mixed markers and sections.", $node);
        }

        return $children;
    }

    /**
     * @inheritDoc
     */
    public function supports (HtmlNode $node) : bool
    {
        return $node instanceof ElementNode && "div" === $node->getTagName();
    }
}
