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
        "a" => true,
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
        $children[0]->prependOpeningTag($node->getTagName(), $this->getElementMarkupParameters($node));
        $lastIndex = \count($children) - 1;
        $children[$lastIndex]->addClosingTag();

        return $children;
    }


    /**
     * Returns the markup parameters for the given element
     *
     * @param ElementNode $element
     * @return array
     */
    private function getElementMarkupParameters (ElementNode $element) : array
    {
        $parameters = [];

        if ("a" === $element->getTagName())
        {
            $url = $element->getAttribute("href");
            $parameters["href"] = $url;
            $parameters["rel"] = [
                "url" => $url,
                "inNewWindow" => "_blank" === $element->getAttribute("target"),
            ];
        }

        return $parameters;
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
