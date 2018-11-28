<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc;

use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\MarkupSection;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\Section;


class Document
{
    private $sections = [];

    /**
     * @param Section $section
     */
    public function appendSection (Section $section)
    {
        $this->sections[] = $section;
    }


    /**
     * @param Marker $marker
     */
    public function appendToLastParagraph (Marker $marker) : void
    {
        $lastSection = \end($this->sections);

        if (!$lastSection instanceof MarkupSection || !$lastSection->isParagraph())
        {
            $lastSection = new MarkupSection("p");
            $this->appendSection($lastSection);
        }

        $lastSection->append($marker);
    }


    /**
     * @return Section[]
     */
    public function getSections () : array
    {
        return $this->sections;
    }
}
