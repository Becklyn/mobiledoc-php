<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser;

class ParseResult
{
    private ?array $mobiledoc;
    /** @var string[] */
    private array $logMessages;


    public function __construct (?array $mobiledoc, array $logMessages)
    {
        $this->mobiledoc = $mobiledoc;
        $this->logMessages = $logMessages;
    }


    public function getMobiledoc () : ?array
    {
        return $this->mobiledoc;
    }


    /**
     * @return string[]
     */
    public function getLogMessages () : array
    {
        return $this->logMessages;
    }
}
