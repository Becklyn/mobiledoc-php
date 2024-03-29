<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc;

use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\AtomMarker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\TextMarker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\CardSection;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\ListSection;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\MarkupSection;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\Section;

/**
 * Serializes a mobiledoc document
 */
class DocumentSerializer
{
    private string $version = "0.3.1";
    private array $markups = [];
    private array $markupIndexMapping = [];
    private array $atoms = [];
    private array $cards = [];
    private array $sections;


    /**
     * @param Section[] $sections
     */
    public function __construct (array $sections)
    {
        $this->sections = $this->serializeList($sections);
    }


    private function serializeList (array $list) : array
    {
        $serialized = [];

        foreach ($list as $entry)
        {
            $serialized[] = $this->serializeEntry($entry);
        }

        return $serialized;
    }


    private function serializeEntry (object $entry) : array
    {
        switch (true)
        {
            case $entry instanceof MarkupSection:
                return $this->serializeMarkupSection($entry);

            case $entry instanceof ListSection:
                return $this->serializeListSection($entry);

            case $entry instanceof CardSection:
                return $this->serializeCardSection($entry);

            case $entry instanceof TextMarker:
                return $this->serializeTextMarker($entry);

            case $entry instanceof AtomMarker:
                return $this->serializeAtomMarker($entry);
        }

        throw new \InvalidArgumentException(\sprintf("Can't serialize value of type '%s'.", \get_class($entry)));
    }


    /**
     * Serializes a section: Markup
     */
    private function serializeMarkupSection (MarkupSection $section) : array
    {
        return [
            MobiledocConstants::MARKUP_SECTION,
            $section->getTagName(),
            $this->serializeList($section->getMarkers()),
        ];
    }


    /**
     * Serializes a section: List
     */
    private function serializeListSection (ListSection $section) : array
    {
        return [
            MobiledocConstants::LIST_SECTION,
            $section->getTagName(),
            \array_map([$this, "serializeList"], $section->getListItems()),
        ];
    }


    /**
     * Serializes a section: Card
     */
    private function serializeCardSection (CardSection $section) : array
    {
        $cardIndex = $this->addCard($section);

        return [
            MobiledocConstants::CARD_SECTION,
            $cardIndex,
        ];
    }


    /**
     * Serializes a marker: Text
     */
    private function serializeTextMarker (TextMarker $marker) : array
    {
        return [
            MobiledocConstants::TEXT_MARKER,
            $this->transformOpeningTagsToMarkupIndexes($marker->getOpeningTags()),
            $marker->getClosingTags(),
            $marker->getText(),
        ];
    }


    /**
     * Serializes a marker: Atom
     */
    private function serializeAtomMarker (AtomMarker $marker) : array
    {
        $atomIndex = $this->addAtom($marker);

        return [
            MobiledocConstants::ATOM_MARKER,
            $this->transformOpeningTagsToMarkupIndexes($marker->getOpeningTags()),
            $marker->getClosingTags(),
            $atomIndex,
        ];
    }


    /**
     * Transforms opening tag names to markup indexes
     *
     * @param array[] $openingTags
     */
    private function transformOpeningTagsToMarkupIndexes (array $openingTags) : array
    {
        $indexes = [];

        foreach ($openingTags as $tag)
        {
            [$tagName, $parameters] = $tag;

            // if the tag has parameters, never look at the index
            if (!empty($parameters))
            {
                $index = \count($this->markups);
                $this->markups[] = $tag;
                $indexes[] = $index;
                continue;
            }

            $index = $this->markupIndexMapping[$tagName] ?? null;

            if (null === $index)
            {
                $index = \count($this->markups);
                $this->markups[] = [$tagName];
                $this->markupIndexMapping[$tagName] = $index;
            }

            $indexes[] = $index;
        }

        return $indexes;
    }


    /**
     * Adds the atom and returns the index for it
     */
    private function addAtom (AtomMarker $atom) : int
    {
        $index = \count($this->atoms);
        $this->atoms[] = [
            $atom->getName(),
            $atom->getText(),
            $atom->getPayload(),
        ];

        return $index;
    }


    /**
     * Adds the card and returns the index for it
     */
    private function addCard (CardSection $card) : int
    {
        $index = \count($this->cards);
        $this->cards[] = [
            $card->getName(),
            $card->getPayload(),
        ];

        return $index;
    }


    /**
     * Serialize the markups list
     */
    private function serializeMarkups (array $markups) : array
    {
        $result = [];

        foreach ($markups as $markup)
        {
            $tagName = $markup[0];
            $parameters = $markup[1] ?? [];

            if (empty($parameters))
            {
                $result[] = [$tagName];
                continue;
            }

            $serializesParameters = [];

            foreach ($parameters as $name => $value)
            {
                $serializesParameters[] = $name;
                $serializesParameters[] = $value;
            }

            $result[] = [$tagName, $serializesParameters];
        }

        return $result;
    }


    /**
     */
    public function serialize () : array
    {
        return [
            "version" => $this->version,
            "markups" => $this->serializeMarkups($this->markups),
            "atoms" => $this->atoms,
            "cards" => $this->cards,
            "sections" => $this->sections,
        ];
    }
}
