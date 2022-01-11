<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Parser;

class ParseLogger
{
    private $messages = [];


    /**
     * @param string ...$messages
     */
    public function log (...$messages) : void
    {
        $this->messages[] = \sprintf(...$messages);
    }


    /**
     */
    public function getMessages () : array
    {
        return $this->messages;
    }
}
