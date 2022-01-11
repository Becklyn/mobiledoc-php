<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Exception;

use Becklyn\Mobiledoc\Parser\Html\Node\ElementNode;
use Becklyn\Mobiledoc\Parser\Html\Node\HtmlNode;

class ParseException extends MobiledocException
{
    /**
     * @var HtmlNode|\DOMElement|\DOMNode|null
     */
    private $node;


    /**
     * @param HtmlNode|\DOMNode|null $node
     */
    public function __construct (string $message, $node = null, ?\Throwable $throwable = null)
    {
        parent::__construct($message, $throwable);
        $this->node = $node;
        $this->message = $message;
    }


    /**
     */
    public function getFullMessage () : string
    {
        $message = $this->message;
        $node = $this->node;

        if ($node instanceof ElementNode)
        {
            $node = $node->getElement();
        }

        if ($node instanceof \DOMNode)
        {
            $message .= " In HTML: '{$node->ownerDocument->saveHTML($node)}'";
        }

        return $message;
    }
}
