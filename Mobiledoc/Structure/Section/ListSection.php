<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Section;


class ListSection implements Section
{
    /**
     * @var string
     */
    private $tagName;


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
}
