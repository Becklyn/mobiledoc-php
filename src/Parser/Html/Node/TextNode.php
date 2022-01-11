<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\Node;

class TextNode implements HtmlNode
{
    private string $text;


    public function __construct (\DOMText $node)
    {
        $this->text = $node->textContent;
    }


    public function appendText (string $text) : void
    {
        $this->text .= $text;
    }


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
