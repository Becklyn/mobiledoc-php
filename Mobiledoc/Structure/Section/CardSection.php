<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Section;


class CardSection implements Section
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
     * @param array  $payload
     */
    public function __construct (string $name, array $payload = [])
    {
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
