<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Renderer;

use Becklyn\Mobiledoc\Mobiledoc\MobiledocConstants;
use Becklyn\Mobiledoc\Mobiledoc\SimpleDocument;
use Becklyn\Mobiledoc\Extension\ExtensionRegistry;
use Becklyn\Mobiledoc\Renderer\Markup\MarkupAttributesVisitor;


/**
 * Renders a mobiledoc
 */
class RenderProcess
{
    /**
     * @var array
     */
    private $mobiledoc;


    /**
     * @var ExtensionRegistry
     */
    private $extensionRegistry;


    /**
     * @var string
     */
    private $text;


    /**
     * @var array
     */
    private $markupStack = [];


    /**
     * @var MarkupAttributesVisitor[]
     */
    private $markupAttributesVisitors = [];


    /**
     * @param array                     $mobiledoc
     * @param ExtensionRegistry         $extensionRegistry
     * @param MarkupAttributesVisitor[] $markupAttributesVisitors
     */
    public function __construct (array $mobiledoc, ExtensionRegistry $extensionRegistry, array $markupAttributesVisitors = [])
    {
        $this->extensionRegistry = $extensionRegistry;
        $this->mobiledoc = $mobiledoc;
        $this->markupAttributesVisitors = $markupAttributesVisitors;

        // render document
        $this->text = $this->render();
    }


    /**
     * @return string
     */
    private function render ()
    {
        $result = [];

        foreach ($this->mobiledoc["sections"] as $section)
        {
            switch ($section[0])
            {
                case MobiledocConstants::MARKUP_SECTION:
                    $result[] = $this->renderTextSection($section[1], $section[2]);
                    break;

                case MobiledocConstants::IMAGE_SECTION:
                    $result[] = $this->renderImageSection($section[1]);
                    break;

                case MobiledocConstants::LIST_SECTION:
                    $result[] = $this->renderListSection($section[1], $section[2]);
                    break;

                case MobiledocConstants::CARD_SECTION:
                    $result[] = $this->renderCardSection($section[1]);
                    break;
            }
        }

        return \implode("", $result);
    }


    /**
     * Renders a text section
     *
     * @param string $tagName
     * @param array  $markers
     * @return string
     */
    private function renderTextSection (string $tagName, array $markers) : string
    {
        return "<{$tagName}>" . $this->renderMarkers($markers) . "</{$tagName}>";
    }


    /**
     * @param string $src
     * @return string
     */
    private function renderImageSection (string $src) : string
    {
        return '<img src="' . $src . '" alt="">';
    }


    /**
     * Renders a list
     *
     * @param string $tagName
     * @param array  $listElements
     * @return string
     */
    private function renderListSection (string $tagName, array $listElements) : string
    {
        $content = [];

        foreach ($listElements as $markersInListElement)
        {
            $content[] = "<li>{$this->renderMarkers($markersInListElement)}</li>";
        }

        return "<{$tagName}>" . \implode("", $content) . "</{$tagName}>";
    }


    /**
     * Renders a card
     *
     * @param array $cardConfig
     * @return string
     */
    private function renderCardSection (int $cardIndex) : string
    {
        [$identifier, $payload] = $this->mobiledoc["cards"][$cardIndex] ?? [];
        $card = $this->extensionRegistry->getExtension($identifier);

        return null !== $card
            ? $card->render(null, $payload)
            : "";
    }


    /**
     * Renders a list of markers
     *
     * @param array $markers
     * @return string
     */
    private function renderMarkers (array $markers) : string
    {
        $text = [];

        foreach ($markers as $marker)
        {
            switch ($marker[0])
            {
                case 0:
                    $text[] = $this->renderTextMarker($marker[1], $marker[2], $marker[3]);
                    break;

                case 1:
                    $text[] = $this->renderAtomMarker($marker[1], $marker[2], $marker[3]);
                    break;
            }
        }

        return \implode("", $text);
    }


    /**
     * Renders a text marker
     *
     * @param array  $openMarkupIndexes
     * @param int    $numberOfClosedMarkups
     * @param string $text
     * @return string
     */
    private function renderTextMarker (array $openMarkupIndexes, int $numberOfClosedMarkups, string $text) : string
    {
        return $this->wrapTextWithMarker($openMarkupIndexes, $numberOfClosedMarkups, \htmlspecialchars($text, \ENT_QUOTES));
    }


    /**
     * Renders an atom marker
     *
     * @param array $openMarkupIndexes
     * @param int   $numberOfClosedMarkups
     * @param int   $atomIndex
     * @return string
     */
    private function renderAtomMarker (array $openMarkupIndexes, int $numberOfClosedMarkups, int $atomIndex) : string
    {
        [$identifier, $textContent, $payload] = $this->mobiledoc["atoms"][$atomIndex] ?? [];
        $atom = $this->extensionRegistry->getExtension($identifier);

        $result = null !== $atom
            ? $atom->render($textContent, $payload)
            : \htmlspecialchars($textContent, \ENT_QUOTES);

        return $this->wrapTextWithMarker($openMarkupIndexes, $numberOfClosedMarkups, $result);
    }


    /**
     * Wraps the given text correctly with the markup
     *
     * @param array  $openMarkupIndexes
     * @param int    $numberOfClosedMarkups
     * @param string $text
     * @return string
     */
    private function wrapTextWithMarker (array $openMarkupIndexes, int $numberOfClosedMarkups, string $text) : string
    {
        $openingTags = [];
        $closingTags = [];

        foreach ($openMarkupIndexes as $opened)
        {
            $openingMarkup = $this->mobiledoc["markups"][$opened];
            $openingTags[] = $this->renderOpeningMarkup($openingMarkup);
            $this->markupStack[] = $openingMarkup[0];
        }

        for ($i = 0; $i < $numberOfClosedMarkups; $i++)
        {
            $closingTag = \array_pop($this->markupStack);
            $closingTags[] = "</{$closingTag}>";
        }

        return \implode("", $openingTags) . $text . \implode("", $closingTags);
    }


    /**
     * Renders an opening markup tag
     *
     * @param array $markup
     * @return string
     */
    private function renderOpeningMarkup (array $markup) : string
    {
        $tagName = $markup[0];
        $flatAttributes = $markup[1] ?? null;

        // if only an opening tag or invalid attributes
        if (!\is_array($flatAttributes))
        {
            return "<{$tagName}>";
        }

        // parse attribute list to a structured array
        $attributes = $this->parseFlatAttributes($flatAttributes);

        foreach ($this->markupAttributesVisitors as $visitor)
        {
            $transformed = $visitor->transform($tagName, $attributes);

            if (null !== $transformed)
            {
                $attributes = $transformed;
                break;
            }
        }

        foreach ($attributes as $key => $value)
        {
            // skip `false` and `null` attributes
            if ($value === false || $value === null)
            {
                continue;
            }

            // for `true` attributes, just use the key
            if ($value === true)
            {
                $renderedAttributes[] = $key;
                continue;
            }

            // skip values, that are not strings (to be forward compatible with structured markup contexts)
            if (!\is_string($value) && !\is_int($value) && !\is_float($value))
            {
                continue;
            }

            $renderedAttributes[] = $key . '="' . \htmlspecialchars((string) $value, \ENT_QUOTES) . '"';
        }

        return "<{$tagName} " . \implode(" ", $renderedAttributes) .  ">";
    }


    /**
     * @return SimpleDocument
     */
    public function getRenderedDocument () : SimpleDocument
    {
        return new SimpleDocument($this->mobiledoc, $this->text);
    }


    /**
     * Parses the flat attribute list into a structured attribute map.
     *
     * If there are duplicate keys in the list, the last one will overwrite all previous ones in the map.
     *
     * @param array $attributes
     * @return array
     */
    private function parseFlatAttributes (array $attributes) : array
    {
        $numberOfEntries = \floor(\count($attributes) / 2);
        $structured = [];

        for ($i = 0; $i < $numberOfEntries; $i++)
        {
            $key = $attributes[$i * 2];
            $value = $attributes[($i * 2) + 1];
            $structured[$key] = $value;
        }

        // if the array has an odd number of entries, just add the last remaining key with `null` as value
        $lastIndex = \count($attributes) - 1;
        if ($lastIndex % 2 === 0)
        {
            $structured[$attributes[$lastIndex]] = null;
        }

        return $structured;
    }
}
