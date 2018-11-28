<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\ElementParser;


use Becklyn\Mobiledoc\Exception\ParseException;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\TextMarker;
use Becklyn\Mobiledoc\Parser\Html\Node\ElementNode;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;
use Becklyn\Mobiledoc\Parser\Html\Node\TextNode;


class InlineParser implements ElementParser
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
     *
     * @param HtmlNode $node
     */
    public function parse (HtmlNode $node) : array
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
            // if it is an element, that may not occur here, throw an error
            if (!$this->supports($child))
            {
                throw new ParseException(sprintf("Can't inline parse nested element: %s", $child->getDebugLabel()));
            }

            // valid nested element, so continue parsing
            foreach ($this->parse($child) as $childMarker)
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
        // Text nodes can be parsed
        if ($node instanceof TextNode)
        {
            return true;
        }

        /** @var ElementNode $node */
        return self::VALID_TAG_NAMES[$node->getTagName()] ?? false;
    }
}