<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\ElementParser;


use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\MarkupSection;
use Becklyn\Mobiledoc\Parser\Html\Node\ElementNode;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;


class BlockParser implements ElementParser
{
    private const VALID_TAG_NAMES = [
        "h1" => "h1",
        "h2" => "h2",
        "h3" => "h3",
        "h4" => "h4",
        "h5" => "h5",
        "h6" => "h6",
        "p" => "p",
        "blockquote" => "blockquote",
        "div" => "p",
    ];


    /**
     * @var InlineParser
     */
    private $inlineParser;


    /**
     *
     */
    public function __construct ()
    {
        $this->inlineParser = new InlineParser();
    }


    /**
     * @inheritDoc
     *
     * @param ElementNode $node
     */
    public function parse (HtmlNode $node) : array
    {
        $resolvedTagName = self::VALID_TAG_NAMES[$node->getTagName()];
        $section = new MarkupSection($resolvedTagName);

        foreach ($node->getChildren() as $child)
        {
            foreach ($this->inlineParser->parse($child) as $marker)
            {
                $section->append($marker);
            }
        }

        return [$section];
    }


    /**
     * @inheritDoc
     */
    public function supports (HtmlNode $node) : bool
    {
        // everything else except certain DOM elements are not supported
        if (!$node instanceof ElementNode)
        {
            return false;
        }

        return isset(self::VALID_TAG_NAMES[$node->getTagName()]);
    }
}
