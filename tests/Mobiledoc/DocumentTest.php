<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\tests\Mobiledoc;

use Becklyn\Mobiledoc\Mobiledoc\Document;
use PHPUnit\Framework\TestCase;


class DocumentTest extends TestCase
{
    public function testAccessors ()
    {
        $mobiledoc = ["version" => "0.3.1"];
        $html = '<div>test</div>';
        $document = new Document($mobiledoc, $html);

        self::assertSame($mobiledoc, $document->getMobiledoc());
        self::assertSame($html, $document->getHtml());
    }
}
