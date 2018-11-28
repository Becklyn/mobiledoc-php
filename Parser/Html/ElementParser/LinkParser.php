<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\ElementParser;

use Becklyn\Mobiledoc\Exception\ParseException;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\AtomMarker;
use Becklyn\Mobiledoc\Parser\Html\HtmlNodeParser;
use Becklyn\Mobiledoc\Parser\Html\Node\ElementNode;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;
use Becklyn\Mobiledoc\Parser\Html\Node\TextNode;


class LinkParser implements ElementParserInterface
{
    /**
     * @inheritDoc
     *
     * @param ElementNode $node
     */
    public function parse (HtmlNode $node, HtmlNodeParser $nodeParser) : array
    {
        $children = $node->getChildren();

        $content = $children[0] ?? null;

        // if empty link, just don't parse at all
        if (null === $content)
        {
            return [];
        }

        if (count($children) > 1 || !$content instanceof TextNode)
        {
            throw new ParseException("Can't parse link with nested elements.", $node);
        }

        $domNode = $node->getElement();

        return [
            new AtomMarker("link", $content->getText(), [
                "label" => $content->getText(),
                "url" => $domNode->getAttribute("href"),
                "inNewWindow" => "_blank" === $domNode->getAttribute("target"),
            ]),
        ];
    }


    /**
     * @inheritDoc
     */
    public function supports (HtmlNode $node) : bool
    {
        return $node instanceof ElementNode && "a" === $node->getTagName();
    }

}
