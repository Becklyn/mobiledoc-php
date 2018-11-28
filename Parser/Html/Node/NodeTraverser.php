<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\Node;


use Becklyn\Mobiledoc\Exception\ParseException;


class NodeTraverser
{
    const PRESERVE_WHITSPACE_TEXT_NODES = false;
    const STRIP_WHITSPACE_TEXT_NODES = true;

    /**
     * @param \DOMNode $node
     * @param bool     $stripEmptyTextNodes
     * @return HtmlNode[]
     */
    public static function getSanitizedChildren (\DOMNode $node, bool $stripEmptyTextNodes = self::PRESERVE_WHITSPACE_TEXT_NODES) : array
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
                if ($stripEmptyTextNodes && "" === trim($child->textContent))
                {
                    continue;
                }

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
