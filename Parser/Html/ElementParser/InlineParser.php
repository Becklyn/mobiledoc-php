<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\ElementParser;

use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\TextMarker;
use Becklyn\Mobiledoc\Parser\Html\HtmlNodeParser;
use Becklyn\Mobiledoc\Parser\Html\Node\ElementNode;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;
use Becklyn\Mobiledoc\Parser\Html\Node\TextNode;


class InlineParser implements ElementParserInterface
{
    private const VALID_TAG_NAMES = [
        "b" => true,
        "code" => true,
        "em" => true,
        "i" => true,
        "s" => true,
        "strong" => true,
        "sub" => true,
        "sup" => true,
        "u" => true,
    ];


    /**
     * @inheritDoc
     */
    public function parse (HtmlNode $node, HtmlNodeParser $nodeParser) : array
    {
        if ($node instanceof TextNode)
        {
            return [
                new TextMarker($node->getText()),
            ];
        }

        /** @var Marker[] $children */
        /** @var ElementNode $node */
        $children = [];

        // parse children
        foreach ($node->getChildren() as $child)
        {
            // valid nested element, so continue parsing
            foreach ($nodeParser->parseInline($child, $node) as $childMarker)
            {
                $children[] = $childMarker;
            }
        }

        // if there are no children, just skip the tag
        if (empty($children))
        {
            return [];
        }

        // wrap children correctly in markup
        $children[0]->prependOpeningTag(\strtolower($node->getTagName()));
        $lastIndex = \count($children) - 1;
        $children[$lastIndex]->addClosingTag();

        return $children;
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

        /** @var ElementNode $node */
        return self::VALID_TAG_NAMES[$node->getTagName()] ?? false;
    }
}
