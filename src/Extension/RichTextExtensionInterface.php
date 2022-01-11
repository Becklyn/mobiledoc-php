<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Extension;

/**
 * A rich text extension is either a card or an atom.
 */
interface RichTextExtensionInterface
{
    /**
     * Returns the identifier of the atom
     */
    public function getName () : string;


    /**
     * Renders the rich text.
     *
     * Atoms are passed the content + payload, cards are only passed the payload (content will be `null`).
     */
    public function render (?string $content, array $payload) : string;
}
