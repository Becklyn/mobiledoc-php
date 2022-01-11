<?php declare(strict_types=1);

namespace Tests\Becklyn\Mobiledoc\Extension;

use Becklyn\Mobiledoc\Exception\DuplicateExtensionMobiledocException;
use Becklyn\Mobiledoc\Extension\ExtensionRegistry;
use PHPUnit\Framework\TestCase;
use Tests\Becklyn\Mobiledoc\Fixtures\TestExtension;


class ExtensionRegistryTest extends TestCase
{
    public function testRegistration () : void
    {
        $extension = new TestExtension();
        $registry = new ExtensionRegistry();

        self::assertNull($registry->getExtension("test"));
        $registry->registerExtension($extension);
        self::assertSame($extension, $registry->getExtension("test"));
    }


    public function testConstructorInjection () : void
    {
        $extension = new TestExtension();
        $registry = new ExtensionRegistry([$extension]);

        self::assertSame($extension, $registry->getExtension("test"));
    }


    public function testDuplicateRegistration () : void
    {
        $this->expectException(DuplicateExtensionMobiledocException::class);

        $extension = new TestExtension();
        $registry = new ExtensionRegistry();

        $registry->registerExtension($extension);
        $registry->registerExtension($extension);
    }
}
