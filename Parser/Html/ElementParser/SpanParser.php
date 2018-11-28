<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\ElementParser;

use Becklyn\Mobiledoc\Parser\Html\HtmlNodeParser;
use Becklyn\Mobiledoc\Parser\Html\Node\ElementNode;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;


/**
 * A <span> is just compiled away and is completely replaced with its children
 */
class SpanParser implements ElementParserInterface
{
    /**
     * @inheritDoc
     *
     * @param ElementNode $node
     */
    public function parse (HtmlNode $node, HtmlNodeParser $nodeParser) : array
    {
        $elements = [];

        foreach ($node->getChildren() as $child)
        {
            foreach ($nodeParser->parseInline($child, $node) as $nested)
            {
                $elements[] = $nested;
            }
        }

        return $elements;
    }


    /**
     * @inheritDoc
     */
    public function supports (HtmlNode $node) : bool
    {
        return $node instanceof ElementNode && "span" === $node->getTagName();
    }

}
