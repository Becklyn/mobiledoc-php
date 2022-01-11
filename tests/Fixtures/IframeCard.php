<?php declare(strict_types=1);

namespace Tests\Becklyn\Mobiledoc\Fixtures;

use Becklyn\Mobiledoc\Extension\RichTextExtensionInterface;


class IframeCard implements RichTextExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function getName () : string
    {
        return "iframe";
    }


    /**
     * @inheritDoc
     */
    public function render (?string $content, array $payload) : string
    {
        return '<iframe src="' . $payload["src"] . '"></iframe>';
    }

}
