<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc;

use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\MarkupSection;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\Section;


class Document
{
    /**
     * @var Section[]
     */
    private $sections = [];


    /**
     * @var MarkupSection|null
     */
    private $lastAutomaticallyCreatedSection;

    /**
     * @param Section $section
     */
    public function appendSection (Section $section)
    {
        $this->sections[] = $section;
        // reset the automatically created section
        $this->lastAutomaticallyCreatedSection = null;
    }


    /**
     * @param Marker $marker
     */
    public function appendToLastParagraph (Marker $marker) : void
    {
        if (null === $this->lastAutomaticallyCreatedSection)
        {
            $section = new MarkupSection("p");
            $this->appendSection($section);
            $this->lastAutomaticallyCreatedSection = $section;
        }

        $this->lastAutomaticallyCreatedSection->append($marker);
    }


    /**
     * @return Section[]
     */
    public function getNonEmptySections () : array
    {
        $nonEmpty = [];

        foreach ($this->sections as $section)
        {
            if (!$section->isEmpty())
            {
                $nonEmpty[] = $section;
            }
        }

        return $nonEmpty;
    }
}
