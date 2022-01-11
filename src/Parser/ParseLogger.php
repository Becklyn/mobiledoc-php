<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser;

class ParseLogger
{
    /** @var string[] */
    private array $messages = [];


    /**
     * @param string ...$messages
     */
    public function log (...$messages) : void
    {
        $this->messages[] = \sprintf(...$messages);
    }


    /**
     * @return string[]
     */
    public function getMessages () : array
    {
        return $this->messages;
    }
}
