<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\Node;


use Becklyn\Mobiledoc\Exception\ParseException;


class NodeTraverser
{
    /**
     * @param \DOMNode $node
     * @return HtmlNode[]
     */
    public static function getSanitizedChildren (\DOMNode $node) : array
    {
        $children = [];

        foreach ($node->childNodes as $child)
        {
            // skip comments and document types
            if ($child instanceof \DOMComment || $child instanceof \DOMDocumentType)
            {
                continue;
            }

            if ($child instanceof \DOMText)
            {
                $lastIndex = \count($children) - 1;
                $lastElement = $children[$lastIndex] ?? null;

                if ($lastElement instanceof TextNode)
                {
                    $lastElement->appendText($child->textContent);
                }
                else
                {
                    $children[] = new TextNode($child);
                }

                continue;
            }

            if (!$child instanceof \DOMElement)
            {
                throw new ParseException(\sprintf("Encountered unexpected DOM node of type '%s'.", \get_class($child)));
            }

            $children[] = new ElementNode($child);
        }

        return $children;
    }
}
