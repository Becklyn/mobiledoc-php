<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser\Html\Node;


interface HtmlNode
{
    /**
     * Returns a label for debug purposes
     *
     * @return string
     */
    public function getDebugLabel () : string;
}
