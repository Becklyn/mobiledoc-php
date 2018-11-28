<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Marker;

use Becklyn\Mobiledoc\Mobiledoc\Structure\ContentElement;


abstract class Marker implements ContentElement
{
    /**
     * @var string[]
     */
    private $openingTags;


    /**
     * @var int
     */
    private $closingTags;


    /**
     * @var string
     */
    private $text;


    /**
     *
     * @param string $text
     * @param array  $openingTags
     * @param int    $closingTags
     */
    public function __construct (string $text, array $openingTags = [], int $closingTags = 0)
    {
        $this->openingTags = $openingTags;
        $this->closingTags = $closingTags;
        $this->text = $text;
    }


    /**
     * @return array
     */
    public function getOpeningTags () : array
    {
        return $this->openingTags;
    }


    /**
     * @return int
     */
    public function getClosingTags () : int
    {
        return $this->closingTags;
    }


    /**
     * @return string
     */
    public function getText () : string
    {
        return $this->text;
    }


    /**
     * @param array $tag
     */
    public function prependOpeningTag (string $tag) : void
    {
        \array_unshift($this->openingTags, $tag);
    }


    /**
     *
     */
    public function addClosingTag () : void
    {
        $this->closingTags += 1;
    }


    /**
     * @return bool
     */
    public function isEmpty () : bool
    {
        return "" === trim($this->text);
    }
}
