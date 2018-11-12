<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Renderer;

use Becklyn\Mobiledoc\Mobiledoc\Document;
use Becklyn\Mobiledoc\Extension\ExtensionRegistry;


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
     *
     * @param array             $mobiledoc
     * @param ExtensionRegistry $extensionRegistry
     */
    public function __construct (array $mobiledoc, ExtensionRegistry $extensionRegistry)
    {
        $this->extensionRegistry = $extensionRegistry;
        $this->mobiledoc = $mobiledoc;
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
                case 1:
                    $result[] = $this->renderTextSection($section[1], $section[2]);
                    break;

                case 2:
                    $result[] = $this->renderImageSection($section[1]);
                    break;

                case 3:
                    $result[] = $this->renderListSection($section[1], $section[2]);
                    break;

                case 10:
                    $result[] = $this->renderCardSection($section[1]);
                    break;
            }
        }

        return implode("", $result);
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

        return implode("", $openingTags) . $text . \implode("", $closingTags);
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
        $attributes = $markup[1] ?? null;

        // if only an opening tag
        if (null === $attributes)
        {
            return "<{$tagName}>";
        }

        $numberOfAttributes = count($attributes) / 2;
        $renderedAttributes = [];
        for ($i = 0; $i < $numberOfAttributes; $i++)
        {
            $renderedAttributes[] = $attributes[$i * 2] . '="' . \htmlspecialchars($attributes[($i * 2) + 1], \ENT_QUOTES) . '"';
        }

        return "<{$tagName} " . \implode(" ", $renderedAttributes) .  ">";
    }


    /**
     * @return Document
     */
    public function getRenderedDocument () : Document
    {
        return new Document($this->mobiledoc, $this->text);
    }
}
