<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Marker;

class AtomMarker extends Marker
{
    private string $name;
    private array $payload;


    public function __construct (string $name, string $text, array $payload = [], array $openedMarkups = [], int $closedMarkups = 0)
    {
        parent::__construct($text, $openedMarkups, $closedMarkups);
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
        // an atom is never empty
        return false;
    }
}
