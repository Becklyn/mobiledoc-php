<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\tests\Fixtures;

use Becklyn\Mobiledoc\Extension\RichTextExtensionInterface;


class ExampleAtom implements RichTextExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function getName () : string
    {
        return "example-atom";
    }


    /**
     * @inheritDoc
     */
    public function render (?string $content, array $payload) : string
    {
        return \sprintf(
            '<span class="example-atom" title="%s">%s</span>',
            \htmlspecialchars($payload["title"] ?? "", \ENT_QUOTES),
            \htmlspecialchars($content, \ENT_QUOTES)
        );
    }
}
