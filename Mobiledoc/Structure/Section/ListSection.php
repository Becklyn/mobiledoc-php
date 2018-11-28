<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Section;

use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;


class ListSection implements Section
{
    /**
     * @var string
     */
    private $tagName;


    /**
     * @var Marker[][]
     */
    private $listItems = [];


    public function __construct (string $tagName)
    {
        $this->tagName = $tagName;
    }


    /**
     * Appends a list items containing of all its markers
     *
     * @param Marker[] $markers
     */
    public function appendListItem (array $markers) : void
    {
        $this->listItems[] = $markers;
    }


    /**
     * @return string
     */
    public function getTagName () : string
    {
        return $this->tagName;
    }


    /**
     * @inheritDoc
     */
    public function isEmpty () : bool
    {
        return empty($this->listItems);
    }


    /**
     * @return array
     */
    public function getListItems () : array
    {
        return $this->listItems;
    }
}
