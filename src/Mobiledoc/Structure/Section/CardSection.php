<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Section;

class CardSection implements Section
{
    private string $name;
    private array $payload;


    public function __construct (string $name, array $payload = [])
    {
        $this->name = $name;
        $this->payload = $payload;
    }


    public function getName () : string
    {
        return $this->name;
    }


    public function getPayload () : array
    {
        return $this->payload;
    }


    /**
     * @inheritDoc
     */
    public function isEmpty () : bool
    {
        return false;
    }
}
