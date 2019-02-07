<?php declare(strict_types=1);

namespace Tests\Becklyn\Mobiledoc\Renderer;

use Becklyn\Mobiledoc\Extension\ExtensionRegistry;
use Becklyn\Mobiledoc\Extension\RichTextExtensionInterface;
use Becklyn\Mobiledoc\Renderer\Markup\MarkupAttributesVisitor;
use Becklyn\Mobiledoc\Renderer\MobiledocRenderer;
use Becklyn\Mobiledoc\Renderer\RenderProcess;
use Becklyn\Mobiledoc\tests\Fixtures\ExampleAtom;
use Becklyn\Mobiledoc\tests\Fixtures\IframeCard;
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
            "headings should receive h*-class" => [
                [
                    "sections" => [
                        [1, "h1", [
                            [0, [], 0, "oh hai"],
                        ]],
                        [1, "h2", [
                            [0, [], 0, "oh hai"],
                        ]],
                        [1, "h3", [
                            [0, [], 0, "oh hai"],
                        ]],
                        [1, "h4", [
                            [0, [], 0, "oh hai"],
                        ]],
                        [1, "h5", [
                            [0, [], 0, "oh hai"],
                        ]],
                        [1, "h6", [
                            [0, [], 0, "oh hai"],
                        ]],
                    ],
                ],
                '<h1 class="h1">oh hai</h1><h2 class="h2">oh hai</h2><h3 class="h3">oh hai</h3><h4 class="h4">oh hai</h4><h5 class="h5">oh hai</h5><h6 class="h6">oh hai</h6>',
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
            "image section" => [
                [
                    "sections" => [
                        [2, "https://becklyn.com/example.png"],
                    ],
                ],
                '<img src="https://becklyn.com/example.png" alt="">',
            ],
            "ignore non-array attributes" => [
                [
                    "markups" => [
                        ["a", ["href", "http://example.org", "rel", ["an" => "array"]]],
                    ],
                    "sections" => [
                        [1, "p", [
                            [0, [0], 1, "Link Text"],
                        ]],
                    ],
                ],
                '<p><a href="http://example.org">Link Text</a></p>'
            ]
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
        self::assertSame($expectedResult, $renderer->render($mobiledoc));
    }


    /**
     *
     */
    public function testEmptyRendering ()
    {
        $renderer = new MobiledocRenderer(new ExtensionRegistry());
        self::assertNull($renderer->render(null));
    }


    /**
     * Tests rendering an existing atom
     */
    public function testAtomRendering ()
    {
        $atom = new ExampleAtom();
        $renderer = new MobiledocRenderer(new ExtensionRegistry([$atom]));

        self::assertSame(
            '<p><span class="example-atom" title="my title">content</span></p>',
            $renderer->render([
                "atoms" => [
                    ["example-atom", "content", ["title" => "my title"]],
                ],
                "sections" => [
                    [1, "p", [
                        [1, [], 0, 0],
                    ]],
                ],
            ])
        );
    }


    /**
     * Tests rendering a missing atom – with fallback to just the text content
     */
    public function testMissingAtomRendering ()
    {
        $renderer = new MobiledocRenderer(new ExtensionRegistry());

        self::assertSame(
            '<p>content</p>',
            $renderer->render([
                "atoms" => [
                    ["example-atom", "content", ["title" => "my title"]],
                ],
                "sections" => [
                    [1, "p", [
                        [1, [], 0, 0],
                    ]],
                ],
            ])
        );
    }


    /**
     * Tests that cards are correctly rendered
     */
    public function testCardRendering ()
    {
        $card = new IframeCard();
        $renderer = new MobiledocRenderer(new ExtensionRegistry([$card]));

        self::assertSame(
            '<p>before</p><iframe src="https://becklyn.com"></iframe><p>after</p>',
            $renderer->render([
                "cards" => [
                    ["iframe", ["src" => "https://becklyn.com"]],
                ],
                "sections" => [
                    [1, "p", [
                        [0, [], 0, "before"],
                    ]],
                    [10, 0],
                    [1, "p", [
                        [0, [], 0, "after"],
                    ]],
                ],
            ])
        );
    }


    /**
     * Tests that missing cards are just not rendered
     */
    public function testMissingCardRendering ()
    {
        $renderer = new MobiledocRenderer(new ExtensionRegistry());

        self::assertSame(
            '<p>before</p><p>after</p>',
            $renderer->render([
                "cards" => [
                    ["missing", ["some" => "data"]],
                ],
                "sections" => [
                    [1, "p", [
                        [0, [], 0, "before"],
                    ]],
                    [10, 0],
                    [1, "p", [
                        [0, [], 0, "after"],
                    ]],
                ],
            ])
        );
    }


    /**
     * Tests that the payloads are correctly passed into the extensions
     */
    public function testPayloads ()
    {
        $atom = $this->getMockBuilder(RichTextExtensionInterface::class)
            ->getMock();

        $atom
            ->expects(self::once())
            ->method("getName")
            ->willReturn("atom");

        $atom
            ->expects(self::once())
            ->method("render")
            ->with("text content", ["payload" => "of atom"]);

        $card = $this->getMockBuilder(RichTextExtensionInterface::class)
            ->getMock();

        $card
            ->expects(self::once())
            ->method("getName")
            ->willReturn("card");

        $card
            ->expects(self::once())
            ->method("render")
            ->with(null, ["payload" => "of card"]);


        $renderer = new MobiledocRenderer(new ExtensionRegistry([$atom, $card]));
        $renderer->render([
            "atoms" => [
                ["atom", "text content", ["payload" => "of atom"]],
            ],
            "cards" => [
                ["card", ["payload" => "of card"]],
            ],
            "sections" => [
                [1, "p", [
                    [1, [], 0, 0],
                ]],
                [10, 0],
            ],
        ]);
    }


    /**
     * Tests all different supported values for generating markup
     */
    public function testAttributeMarkupGeneration ()
    {
        $document = [
            "markups" => [
                ["b", ["int", 123, "string", "ohai", "null", null, "false", false, "true", true, "float", 1.23, "surplus-element"]]
            ],
            "sections" => [
                [1, "p", [
                    [0, [0], 1, "Text"]
                ]]
            ]
        ];
        $renderer = new RenderProcess($document, new ExtensionRegistry());
        self::assertSame('<p><b int="123" string="ohai" true float="1.23">Text</b></p>', $renderer->getHtml());
    }


    /**
     * Tests that markup attribute visitors are correctly used
     */
    public function testMarkupAttributesVisitor ()
    {
        $document = [
            "markups" => [
                ["b", ["rel", "ohai"]]
            ],
            "sections" => [
                [1, "p", [
                    [0, [0], 1, "Text"]
                ]]
            ]
        ];

        $visitor = new class implements MarkupAttributesVisitor
        {
            /**
             * @inheritDoc
             */
            public function transform (string $tagName, array $attributes) : ?array
            {
                return ["a" => "b"];
            }
        };

        $renderer = new RenderProcess($document, new ExtensionRegistry(), [$visitor]);
        self::assertSame('<p><b a="b">Text</b></p>', $renderer->getHtml());
    }


    /**
     * Tests that skipped visitors don't modify the result
     */
    public function testSkippedMarkupAttributesVisitor ()
    {

        $document = [
            "markups" => [
                ["b", ["rel", "ohai"]]
            ],
            "sections" => [
                [1, "p", [
                    [0, [0], 1, "Text"]
                ]]
            ]
        ];

        $visitor = new class implements MarkupAttributesVisitor
        {
            /**
             * @inheritDoc
             */
            public function transform (string $tagName, array $attributes) : ?array
            {
                return null;
            }
        };

        $renderer = new RenderProcess($document, new ExtensionRegistry(), [$visitor]);
        self::assertSame('<p><b rel="ohai">Text</b></p>', $renderer->getHtml());
    }


    /**
     * Tests that the first matched visitor changed the result
     */
    public function testMarkupAttributesVisitorOrder ()
    {

        $document = [
            "markups" => [
                ["b", ["rel", "ohai"]]
            ],
            "sections" => [
                [1, "p", [
                    [0, [0], 1, "Text"]
                ]]
            ]
        ];

        $visitor1 = new class implements MarkupAttributesVisitor
        {
            /**
             * @inheritDoc
             */
            public function transform (string $tagName, array $attributes) : ?array
            {
                return ["index" => 1];
            }
        };

        $visitor2 = new class implements MarkupAttributesVisitor
        {
            /**
             * @inheritDoc
             */
            public function transform (string $tagName, array $attributes) : ?array
            {
                return ["index" => 2];
            }
        };

        $renderer = new RenderProcess($document, new ExtensionRegistry(), [$visitor1, $visitor2]);
        self::assertSame('<p><b index="1">Text</b></p>', $renderer->getHtml());
    }
}
