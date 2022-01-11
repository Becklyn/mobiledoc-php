<?php declare(strict_types=1);

namespace Tests\Becklyn\Mobiledoc\Parser\Html;

use Becklyn\Mobiledoc\Parser\Html\HtmlParser;
use DMS\PHPUnitExtensions\ArraySubset\Assert;
use PHPUnit\Framework\TestCase;


class HtmlParserTest extends TestCase
{
    public function provideParsingValid () : \Generator
    {
        foreach (glob(__DIR__ . "/fixtures/valid/*.json") as $mobiledocFile)
        {
            $htmlFile = \preg_replace('~\.json~', ".html", $mobiledocFile);

            if (!\is_file($htmlFile))
            {
                throw new \RuntimeException(sprintf("Missing HTML test case: '%s'", $htmlFile));
            }

            $html = \file_get_contents($htmlFile);
            $mobiledoc = \json_decode(\file_get_contents($mobiledocFile), true);

            if (!\is_array($mobiledoc))
            {
                throw new \RuntimeException(sprintf("Can't parse mobiledoc for test case: '%s'", $mobiledocFile));
            }

            yield [
                $html,
                $mobiledoc,
                \basename($mobiledocFile, "." . \pathinfo($mobiledocFile, \PATHINFO_EXTENSION)),
            ];
        }
    }


    /**
     * @dataProvider provideParsingValid
     *
     * @param string $html
     * @param array  $expectedMobileDoc
     * @param string $file
     */
    public function testParsingValid (string $html, array $expectedMobileDoc, string $file) : void
    {
        $parser = new HtmlParser($html);
        $result = $parser->getResult();

        self::assertEquals([], $result->getLogMessages(), $file);
        Assert::assertArraySubset($expectedMobileDoc, $result->getMobiledoc(), false, $file);
    }


    public function provideParsingInvalid () : \Generator
    {
        foreach (glob(__DIR__ . "/fixtures/invalid/*.html") as $htmlFile)
        {
            yield [
                \file_get_contents($htmlFile),
                \basename($htmlFile, "." . \pathinfo($htmlFile, \PATHINFO_EXTENSION)),
            ];
        }
    }


    /**
     * @dataProvider provideParsingInvalid
     *
     * @param string $html
     * @param string $file
     */
    public function testParsingInvalid (string $html, string $file) : void
    {
        $parser = new HtmlParser($html);
        $result = $parser->getResult();

        self::assertNull($result->getMobiledoc(), $file);
        self::assertNotEquals([], $result->getLogMessages(), $file);
    }


    public function testEmptyHtml () : void
    {
        $parser = new HtmlParser("");
        $result = $parser->getResult();

        $emptyMobileDoc = [
            "markups" => [],
            "atoms" => [],
            "cards" => [],
            "sections" => [],
        ];

        Assert::assertArraySubset($emptyMobileDoc, $result->getMobiledoc());
        self::assertEquals([], $result->getLogMessages());
    }
}
