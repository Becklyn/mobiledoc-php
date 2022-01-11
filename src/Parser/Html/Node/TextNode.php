<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\Node;


class TextNode implements HtmlNode
{
    /**
     * @var string
     */
    private $text;


    /**
     * @param \DOMText $node
     */
    public function __construct (\DOMText $node)
    {
        $this->text = $node->textContent;
    }


    /**
     * @param string $text
     */
    public function appendText (string $text) : void
    {
        $this->text .= $text;
    }


    /**
     * @return string
     */
    public function getText () : string
    {
        return $this->text;
    }


    /**
     * @inheritDoc
     */
    public function getDebugLabel () : string
    {
        return "#text";
    }
}
