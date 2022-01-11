<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Section;

use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;

class MarkupSection implements Section
{
    private string $tagName;
    /** @var Marker[] */
    private array $markers = [];


    public function __construct (string $tagName)
    {
        $this->tagName = $tagName;
    }


    public function append (Marker $marker) : void
    {
        $this->markers[] = $marker;
    }


    public function isParagraph () : bool
    {
        return "p" === $this->tagName;
    }


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
