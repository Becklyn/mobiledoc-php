<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Exception;


use Becklyn\Mobiledoc\Parser\Html\Node\ElementNode;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;


class ParseException extends MobiledocException
{
    /**
     *
     * @param string                    $message
     * @param null|HtmlNode|\DOMNode    $node
     * @param \Throwable|null $throwable
     */
    public function __construct (string $message, $node = null, ?\Throwable $throwable = null)
    {
        if ($node instanceof ElementNode)
        {
            $node = $node->getElement();
        }

        if ($node instanceof \DOMNode)
        {
            $message .= " In HTML: '{$node->ownerDocument->saveHTML($node)}'";
        }

        parent::__construct($message, $throwable);
    }
}
