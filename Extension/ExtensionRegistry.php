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
        $name = $extension->getName();

        if (isset($this->extensions[$name]))
        {
            throw new DuplicateExtensionMobiledocException($name);
        }

        $this->extensions[$name] = $extension;
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
