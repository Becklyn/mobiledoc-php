<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Marker;

use Becklyn\Mobiledoc\Mobiledoc\Structure\ContentElement;

abstract class Marker implements ContentElement
{
    /** @var string[][] */
    private array $openingTags;
    private int $closingTags;
    private string $text;


    /**
     *
     */
    public function __construct (string $text, array $openingTags = [], int $closingTags = 0)
    {
        $this->openingTags = $openingTags;
        $this->closingTags = $closingTags;
        $this->text = $text;
    }


    /**
     * @return array[]
     */
    public function getOpeningTags () : array
    {
        return $this->openingTags;
    }


    public function getClosingTags () : int
    {
        return $this->closingTags;
    }


    public function getText () : string
    {
        return $this->text;
    }


    public function prependOpeningTag (string $tag, array $parameters = []) : void
    {
        \array_unshift($this->openingTags, [$tag, $parameters]);
    }


    public function addClosingTag () : void
    {
        ++$this->closingTags;
    }


    public function isEmpty () : bool
    {
        return "" === \trim($this->text);
    }
}
