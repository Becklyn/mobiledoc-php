<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Extension;

use Becklyn\Mobiledoc\Exception\DuplicateExtensionMobiledocException;


/**
 * Holds all extensions
 */
class ExtensionRegistry
{
    /**
     * @var RichTextExtensionInterface[]
     */
    private $extensions = [];


    /**
     * @param RichTextExtensionInterface[] $extensions
     */
    public function __construct (iterable $extensions = [])
    {
        foreach ($extensions as $extension)
        {
            $this->registerExtension($extension);
        }
    }


    /**
     * @param RichTextExtensionInterface $extension
     */
    public function registerExtension (RichTextExtensionInterface $extension) : void
    {
        if (isset($this->extensions[$extension->getName()]))
        {
            throw new DuplicateExtensionMobiledocException($extension->getName());
        }

        $this->extensions[$extension->getName()] = $extension;
    }


    /**
     * @param string $name
     * @return RichTextExtensionInterface|null
     */
    public function getExtension (string $name) : ?RichTextExtensionInterface
    {
        return $this->extensions[$name] ?? null;
    }
}
