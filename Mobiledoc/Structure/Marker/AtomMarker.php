<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Marker;



class AtomMarker extends Marker
{
    /**
     * @var string
     */
    private $name;


    /**
     * @var array
     */
    private $payload;


    /**
     * @param string $name
     * @param string $text
     * @param array  $payload
     * @param array  $openedMarkups
     * @param int    $closedMarkups
     */
    public function __construct (string $name, string $text, array $payload = [], array $openedMarkups = [], int $closedMarkups = 0)
    {
        parent::__construct($text, $openedMarkups, $closedMarkups);
        $this->name = $name;
        $this->payload = $payload;
    }


    /**
     * @return string
     */
    public function getName () : string
    {
        return $this->name;
    }


    /**
     * @return array
     */
    public function getPayload () : array
    {
        return $this->payload;
    }
}
