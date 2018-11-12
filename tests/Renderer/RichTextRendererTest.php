<?php declare(strict_types=1);

namespace Tests\Becklyn\Mobiledoc\Renderer;

use Becklyn\Mobiledoc\Extension\ExtensionRegistry;
use Becklyn\Mobiledoc\Renderer\MobiledocRenderer;
use PHPUnit\Framework\TestCase;


class RichTextRendererTest extends TestCase
{

    public function provideSimpleRendering () : array
    {
        return [
            "single paragraph" => [
                [
                    "sections" => [
                        [1, "p", [
                            [0, [], 0, "oh hai"],
                        ]],
                    ],
                ],
                "<p>oh hai</p>",
            ],
            "single markup" => [
                [
                    "markups" => [
                        ["strong"],
                    ],
                    "sections" => [
                        [1, "p", [
                            [0, [0], 1, "oh hai"],
                        ]],
                    ],
                ],
                "<p><strong>oh hai</strong></p>",
            ],
            "nested markup" => [
                [
                    "markups" => [
                        ["strong"],
                        ["em"],
                    ],
                    "sections" => [
                        [1, "p", [
                            [0, [], 0, "Test "],
                            [0, [0], 1, "BoldBold"],
                            [0, [1, 0], 1, "italic"],
                            [0, [], 1, "Italic"],
                            [0, [], 0, " only."],
                        ]],
                    ],
                ],
                "<p>Test <strong>BoldBold</strong><em><strong>italic</strong>Italic</em> only.</p>",
            ],
            "link" => [
                [
                    "markups" => [
                        ["a", ["href", "https://becklyn.com", "target", "_blank"]],
                    ],
                    "sections" => [
                        [1, "p", [
                            [0, [0], 1, "link"],
                        ]],
                    ],
                ],
                '<p><a href="https://becklyn.com" target="_blank">link</a></p>',
            ],
            "list ul" => [
                [
                    "sections" => [
                        [3, "ul", [
                            [
                                [0, [], 0, "first"],
                            ],
                            [
                                [0, [], 0, "second"],
                            ],
                            [
                                [0, [], 0, "last"],
                            ],
                        ]]
                    ],
                ],
                '<ul><li>first</li><li>second</li><li>last</li></ul>',
            ],
            "list ol" => [
                [
                    "sections" => [
                        [3, "ol", [
                            [
                                [0, [], 0, "first"],
                            ],
                            [
                                [0, [], 0, "second"],
                            ],
                            [
                                [0, [], 0, "last"],
                            ],
                        ]]
                    ],
                ],
                '<ol><li>first</li><li>second</li><li>last</li></ol>',
            ],
            "all markup" => [
                [
                    "markups" => [
                        ["a", ["href", "link"]],
                        ["b"],
                        ["code"],
                        ["em"],
                        ["i"],
                        ["s"],
                        ["strong"],
                        ["sub"],
                        ["sup"],
                        ["u"],
                    ],
                    "sections" => [
                        [1, "p", [
                            [0, [0], 1, "a"],
                            [0, [1], 1, "b"],
                            [0, [2], 1, "code"],
                            [0, [3], 1, "em"],
                            [0, [4], 1, "i"],
                            [0, [5], 1, "s"],
                            [0, [6], 1, "strong"],
                            [0, [7], 1, "sub"],
                            [0, [8], 1, "sup"],
                            [0, [9], 1, "u"],
                        ]]
                    ],
                ],
                \implode("", [
                    '<p>',
                    '<a href="link">a</a>',
                    '<b>b</b>',
                    '<code>code</code>',
                    '<em>em</em>',
                    '<i>i</i>',
                    '<s>s</s>',
                    '<strong>strong</strong>',
                    '<sub>sub</sub>',
                    '<sup>sup</sup>',
                    '<u>u</u>',
                    '</p>',
                ]),
            ],
        ];
    }


    /**
     * @dataProvider provideSimpleRendering
     *
     * @param array  $mobiledoc
     * @param string $expectedResult
     */
    public function testRendering (array $mobiledoc, string $expectedResult)
    {
        $renderer = new MobiledocRenderer(new ExtensionRegistry());
        self::assertSame($expectedResult, (string) $renderer->render($mobiledoc));
    }


    /**
     *
     */
    public function testEmptyRendering ()
    {
        $renderer = new MobiledocRenderer(new ExtensionRegistry());
        self::assertNull($renderer->render(null));
    }
}
