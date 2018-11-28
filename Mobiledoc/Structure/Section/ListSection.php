<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Section;


class ListSection implements Section
{
    /**
     * @var string
     */
    private $tagName;

    private $listItems = [];


    public function __construct (string $tagName)
    {
        $this->tagName = $tagName;
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
}
