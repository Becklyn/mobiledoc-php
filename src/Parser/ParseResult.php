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
     * @param array|null $mobiledoc
     * @param array      $logMessages
     */
    public function __construct (?array $mobiledoc, array $logMessages)
    {
        $this->mobiledoc = $mobiledoc;
        $this->logMessages = $logMessages;
    }


    /**
     * @return array|null
     */
    public function getMobiledoc () : ?array
    {
        return $this->mobiledoc;
    }


    /**
     * @return array
     */
    public function getLogMessages () : array
    {
        return $this->logMessages;
    }
}
