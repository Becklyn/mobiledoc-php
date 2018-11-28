<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\Node;


class ElementNode implements HtmlNode
{
    /**
     * @var \DOMElement
     */
    private $element;


    /**
     * @param \DOMElement $element
     */
    public function __construct (\DOMElement $element)
    {
        $this->element = $element;
    }


    /**
     * @return \DOMElement
     */
    public function getElement () : \DOMElement
    {
        return $this->element;
    }


    /**
     * @return string
     */
    public function getTagName () : string
    {
        return \strtolower($this->element->tagName);
    }


    /**
     * @return HtmlNode[]
     */
    public function getChildren () : array
    {
        return NodeTraverser::getSanitizedChildren($this->element);
    }


    /**
     * @inheritDoc
     */
    public function getDebugLabel () : string
    {
        return "Element <{$this->getTagName()}>";
    }
}