<?php declare(strict_types=1);

namespace Tests\Becklyn\Mobiledoc\Fixtures;

use Becklyn\Mobiledoc\Extension\RichTextExtensionInterface;


class TestExtension implements RichTextExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function getName () : string
    {
        return "test";
    }


    /**
     * @inheritDoc
     */
    public function render (?string $content, array $payload) : string
    {
        return '<div>test</div>';
    }
}
