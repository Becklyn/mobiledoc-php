<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc;

use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\AtomMarker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Marker\TextMarker;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\CardSection;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\ListSection;
use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\MarkupSection;


class DocumentSerializer
{
    private $version = "0.3.1";
    private $markups = [];
    private $atoms = [];
    private $cards = [];
    private $sections = [];

    /**
     * @param Document $document
     */
    public function __construct (Document $document)
    {
        $this->sections = $this->serializeList($document->getSections());
    }


    /**
     * @param array $list
     * @return array
     */
    private function serializeList (array $list) : array
    {
        $serialized = [];

        foreach ($list as $entry)
        {
            $serialized[] = $this->serializeEntry($entry);
        }

        return $serialized;
    }


    /**
     * @param object $entry
     * @return array
     */
    private function serializeEntry ($entry) : array
    {
        if (!\is_object($entry))
        {
            throw new \InvalidArgumentException(sprintf("Can't serialize non-object, %s given.", \gettype($entry)));
        }

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

        throw new \InvalidArgumentException(sprintf("Can't serialize value of type '%s'.", \get_class($entry)));
    }


    /**
     * Serializes a section: Markup
     *
     * @param MarkupSection $section
     * @return array
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
     *
     * @param ListSection $section
     * @return array
     */
    private function serializeListSection (ListSection $section) : array
    {
        return [
            MobiledocConstants::LIST_SECTION,
            $section->getTagName(),
        ];
    }


    /**
     * Serializes a section: Card
     *
     * @param CardSection $section
     * @return array
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
     *
     * @param TextMarker $marker
     * @return array
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
     *
     * @param AtomMarker $marker
     * @return array
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
     * @param array $openingTags
     * @return array
     */
    private function transformOpeningTagsToMarkupIndexes (array $openingTags) : array
    {
        $indexes = [];

        foreach ($openingTags as $tag)
        {
            $index = $this->markups[$tag] ?? null;

            if (null === $index)
            {
                $index = \count($this->markups);
                $this->markups[$tag] = $index;
            }

            $indexes[] = $index;
        }

        return $indexes;
    }


    /**
     * Adds the atom and returns the index for it
     *
     * @param AtomMarker $atom
     * @return int
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
     *
     * @param CardSection $card
     * @return int
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
     * @return array
     */
    public function serialize () : array
    {
        $markups = [];

        foreach ($this->markups as $tag => $index)
        {
            $markups[] = [$tag];
        }

        return [
            "version" => $this->version,
            "markups" => $markups,
            "atoms" => $this->atoms,
            "cards" => $this->cards,
            "sections" => $this->sections,
        ];
    }
}
