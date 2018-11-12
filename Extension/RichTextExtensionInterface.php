<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Extension;


/**
 * A rich text extension is either a card or an atom.
 */
interface RichTextExtensionInterface
{
    /**
     * Returns the identifier of the atom
     *
     * @return string
     */
    public function getName () : string;


    /**
     * Renders the rich text.
     *
     * Atoms are passed the content + payload, cards are only passed the payload (content will be `null`).
     *
     * @param string|null $content
     * @param array       $payload
     * @return string
     */
    public function render (?string $content, array $payload) : string;
}
