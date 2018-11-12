<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc;

/**
 * A document in both representations
 */
class Document
{
    /**
     * @var array
     */
    private $mobiledoc;


    /**
     * @var string
     */
    private $html;


    /**
     * @param array  $mobiledoc
     * @param string $html
     */
    public function __construct (array $mobiledoc, string $html)
    {
        $this->mobiledoc = $mobiledoc;
        $this->html = $html;
    }


    /**
     * @return array
     */
    public function getMobiledoc () : array
    {
        return $this->mobiledoc;
    }


    /**
     * @return string
     */
    public function getHtml () : string
    {
        return $this->html;
    }


    public function __toString ()
    {
        return $this->html;
    }
}
