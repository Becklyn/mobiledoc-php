<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\Node;

class ElementNode implements HtmlNode
{
    /**
     * @var \DOMElement
     */
    private $element;


    /**
     */
    public function __construct (\DOMElement $element)
    {
        $this->element = $element;
    }


    /**
     */
    public function getElement () : \DOMElement
    {
        return $this->element;
    }


    /**
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
        return NodeTraverser::getSanitizedChildren($this->element, NodeTraverser::PRESERVE_WHITSPACE_TEXT_NODES);
    }


    /**
     * @return HtmlNode[]
     */
    public function getChildrenWithoutWhitespace () : array
    {
        return NodeTraverser::getSanitizedChildren($this->element, NodeTraverser::STRIP_WHITSPACE_TEXT_NODES);
    }


    /**
     * @inheritDoc
     */
    public function getDebugLabel () : string
    {
        return "Element <{$this->getTagName()}>";
    }


    /**
     */
    public function getAttribute (string $name) : string
    {
        return $this->element->getAttribute($name);
    }
}
