<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc;

use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\Marker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\MarkupSection;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\Section;

class Document
{
    /** @var Section[] */
    private array $sections = [];
    private ?MarkupSection $lastAutomaticallyCreatedSection = null;


    public function appendSection (Section $section) : void
    {
        $this->sections[] = $section;
        // reset the automatically created section
        $this->lastAutomaticallyCreatedSection = null;
    }


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
