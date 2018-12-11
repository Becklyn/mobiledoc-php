<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Renderer;

use Becklyn\Mobiledoc\Mobiledoc\SimpleDocument;
use Becklyn\Mobiledoc\Extension\ExtensionRegistry;
use Becklyn\Mobiledoc\Renderer\Markup\MarkupAttributesVisitor;


class MobiledocRenderer
{
    /**
     * @var ExtensionRegistry
     */
    private $extensionRegistry;


    /**
     * @var MarkupAttributesVisitor[]
     */
    private $markupAttributesVisitor = [];


    /**
     * @param ExtensionRegistry         $extensionRegistry
     * @param MarkupAttributesVisitor[] $markupAttributesVisitors
     */
    public function __construct (ExtensionRegistry $extensionRegistry, array $markupAttributesVisitors = [])
    {
        $this->extensionRegistry = $extensionRegistry;

        foreach ($markupAttributesVisitors as $visitor)
        {
            $this->registerMarkupAttributesVisitor($visitor);
        }
    }


    /**
     * @param MarkupAttributesVisitor $visitor
     */
    public function registerMarkupAttributesVisitor (MarkupAttributesVisitor $visitor)
    {
        $this->markupAttributesVisitor[] = $visitor;
    }


    /**
     * Renders the given mobiledoc to a document
     *
     * @param array|null $mobiledoc
     * @return SimpleDocument|null
     */
    public function render (?array $mobiledoc) : ?SimpleDocument
    {
        if (null === $mobiledoc)
        {
            return null;
        }

        $render = new RenderProcess($mobiledoc, $this->extensionRegistry, $this->markupAttributesVisitor);
        return $render->getRenderedDocument();
    }
}
