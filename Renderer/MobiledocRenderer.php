<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Renderer;


use Becklyn\Mobiledoc\Document\Document;
use Becklyn\Mobiledoc\Extension\ExtensionRegistry;


class MobiledocRenderer
{
    /**
     * @var ExtensionRegistry
     */
    private $extensionRegistry;


    /**
     * @param ExtensionRegistry $extensionRegistry
     */
    public function __construct (ExtensionRegistry $extensionRegistry)
    {
        $this->extensionRegistry = $extensionRegistry;
    }


    /**
     * Renders the given mobiledoc to a document
     *
     * @param array|null $mobiledoc
     * @return Document|null
     */
    public function render (?array $mobiledoc) : ?Document
    {
        if (null === $mobiledoc)
        {
            return null;
        }

        $render = new RenderProcess($mobiledoc, $this->extensionRegistry);
        return $render->getRenderedDocument();
    }
}