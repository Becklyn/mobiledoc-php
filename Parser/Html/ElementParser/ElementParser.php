<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\ElementParser;

use Becklyn\Mobiledoc\Mobiledoc\Structure\ContentElement;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;


interface ElementParser
{
    /**
     * Parses the given element into content elements
     *
     * @param HtmlNode $node
     * @return ContentElement[]
     */
    public function parse (HtmlNode $node) : array;


    /**
     * Returns whether the given DOM node can be parsed using this parser
     *
     * @param HtmlNode $node
     * @return bool
     */
    public function supports (HtmlNode $node) : bool;
}
