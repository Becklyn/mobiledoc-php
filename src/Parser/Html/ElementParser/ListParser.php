<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\ElementParser;

use Becklyn\Mobiledoc\Exception\ParseException;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\ListSection;
use Becklyn\Mobiledoc\Parser\Html\HtmlNodeParser;
use Becklyn\Mobiledoc\Parser\Html\Node\ElementNode;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;

class ListParser implements ElementParserInterface
{
    /**
     * @inheritDoc
     *
     * @param ElementNode $node
     */
    public function parse (HtmlNode $node, HtmlNodeParser $nodeParser) : array
    {
        $section = new ListSection($node->getTagName());

        foreach ($node->getChildrenWithoutWhitespace() as $child)
        {
            $section->appendListItem($this->parseListItem($child, $nodeParser));
        }

        return [$section];
    }


    /**
     * @return Marker[]
     */
    private function parseListItem (HtmlNode $node, HtmlNodeParser $nodeParser) : array
    {
        if (!$node instanceof ElementNode)
        {
            throw new ParseException("Can't parse text nodes directly inside of lists.", $node);
        }

        if ("li" !== $node->getTagName())
        {
            throw new ParseException(\sprintf(
                "Can't parse element <%s> directly inside of lists.",
                $node->getTagName()
            ), $node);
        }

        $items = [];
        $children = $node->getChildren();
        $firstChild = $children[0] ?? null;

        // if the only child is a single '<p>'
        if (1 === \count($children) && $firstChild instanceof ElementNode && "p" === $firstChild->getTagName())
        {
            foreach ($firstChild->getChildren() as $child)
            {
                foreach ($nodeParser->parseInline($child, $node) as $nestedItem)
                {
                    $items[] = $nestedItem;
                }
            }
        }
        else
        {
            // just parse as inline
            foreach ($node->getChildren() as $listItemChild)
            {
                foreach ($nodeParser->parseInline($listItemChild, $node) as $nestedItem)
                {
                    $items[] = $nestedItem;
                }
            }
        }

        return $items;
    }


    /**
     * @inheritDoc
     */
    public function supports (HtmlNode $node) : bool
    {
        if (!$node instanceof ElementNode)
        {
            return false;
        }

        return "ul" === $node->getTagName() || "ol" === $node->getTagName();
    }
}
