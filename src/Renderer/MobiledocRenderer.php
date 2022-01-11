<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Renderer;

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
     */
    public function registerMarkupAttributesVisitor (MarkupAttributesVisitor $visitor) : void
    {
        $this->markupAttributesVisitor[] = $visitor;
    }


    /**
     * Renders the given mobiledoc to a document
     */
    public function render (?array $mobiledoc) : ?string
    {
        if (null === $mobiledoc)
        {
            return null;
        }

        $render = new RenderProcess($mobiledoc, $this->extensionRegistry, $this->markupAttributesVisitor);
        return $render->getHtml();
    }
}
