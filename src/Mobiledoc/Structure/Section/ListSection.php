<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Section;

use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;

class ListSection implements Section
{
    private string $tagName;
    /** @var Marker[][] */
    private array $listItems = [];


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


    public function getListItems () : array
    {
        return $this->listItems;
    }
}
