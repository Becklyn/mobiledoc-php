<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\tests\Parser\Html;

use Becklyn\Mobiledoc\Parser\Html\HtmlParser;
use PHPUnit\Framework\TestCase;


class HtmlParserTest extends TestCase
{
    /**
     * @return \Generator
     */
    public function provideParsingValid ()
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

            yield [$html, $mobiledoc];
        }
    }


    /**
     * @dataProvider provideParsingValid
     *
     * @param string $html
     * @param array  $expectedMobileDoc
     */
    public function testParsingValid (string $html, array $expectedMobileDoc) : void
    {
        $parser = new HtmlParser($html);
        $result = $parser->getResult();

        self::assertEquals([], $result->getLogMessages());
        self::assertArraySubset($expectedMobileDoc, $result->getMobiledoc());
    }


    /**
     * @return \Generator
     */
    public function provideParsingInvalid ()
    {
        foreach (glob(__DIR__ . "/fixtures/invalid/*.html") as $htmlFile)
        {
            yield [\file_get_contents($htmlFile)];
        }
    }


    /**
     * @dataProvider provideParsingInvalid
     *
     * @param string $html
     */
    public function testParsingInvalid (string $html) : void
    {
        $parser = new HtmlParser($html);
        $result = $parser->getResult();

        self::assertNull($result->getMobiledoc());
        self::assertNotEquals([], $result->getLogMessages());
    }
}
