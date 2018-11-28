<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Section;

use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;


class MarkupSection implements Section
{
    /**
     * @var string
     */
    private $tagName;


    /**
     * @var Marker[]
     */
    private $markers;


    /**
     * @param string $tagName
     */
    public function __construct (string $tagName)
    {
        $this->tagName = $tagName;
    }


    /**
     * @param Marker $marker
     */
    public function append (Marker $marker) : void
    {
        $this->markers[] = $marker;
    }


    /**
     * @return bool
     */
    public function isParagraph () : bool
    {
        return "p" === $this->tagName;
    }


    /**
     * @return string
     */
    public function getTagName () : string
    {
        return $this->tagName;
    }


    /**
     * @return Marker[]
     */
    public function getMarkers () : array
    {
        return $this->markers;
    }


    /**
     * @inheritDoc
     */
    public function isEmpty () : bool
    {
        foreach ($this->markers as $marker)
        {
            if (!$marker->isEmpty())
            {
                return false;
            }
        }

        return true;
    }
}
