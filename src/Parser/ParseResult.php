<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser;

class ParseResult
{
    /**
     * @var array|null
     */
    private $mobiledoc;


    /**
     * @var array
     */
    private $logMessages;


    /**
     */
    public function __construct (?array $mobiledoc, array $logMessages)
    {
        $this->mobiledoc = $mobiledoc;
        $this->logMessages = $logMessages;
    }


    /**
     */
    public function getMobiledoc () : ?array
    {
        return $this->mobiledoc;
    }


    /**
     */
    public function getLogMessages () : array
    {
        return $this->logMessages;
    }
}
