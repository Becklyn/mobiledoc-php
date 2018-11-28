<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\ElementParser;


use Becklyn\Mobiledoc\Mobiledoc\Structure\Section\MarkupSection;
use Becklyn\Mobiledoc\Parser\Html\Node\ElementNode;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;


class BlockParser implements ElementParser
{
    private const VALID_TAG_NAMES = [
        "h1" => true,
        "h2" => true,
        "h3" => true,
        "h4" => true,
        "h5" => true,
        "h6" => true,
        "p" => true,
        "blockquote" => true,
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
        $section = new MarkupSection($node->getTagName());

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

        return self::VALID_TAG_NAMES[$node->getTagName()] ?? false;
    }
}
