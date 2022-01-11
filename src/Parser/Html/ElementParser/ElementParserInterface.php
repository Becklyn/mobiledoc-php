<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\ElementParser;

use Becklyn\Mobiledoc\Mobiledoc\Structure\ContentElement;
use Becklyn\Mobiledoc\Parser\Html\HtmlNodeParser;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;

interface ElementParserInterface
{
    /**
     * Parses the given element into content elements
     *
     * @return ContentElement[]
     */
    public function parse (HtmlNode $node, HtmlNodeParser $nodeParser) : array;


    /**
     * Returns whether the given DOM node can be parsed using this parser
     */
    public function supports (HtmlNode $node) : bool;
}
