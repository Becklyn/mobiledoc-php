<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure;


class Markups
{
    private $markups = [];

    /**
     * @param array $tags
     * @return array
     */
    public function transformTagsToIndexes (array $tags) : array
    {
        $indexes = [];

        foreach ($tags as $tag)
        {
            $index = $this->markups[$tag] ?? null;

            if (null === $index)
            {
                $index = \count($this->markups);
                $this->markups[$tag] = $index;
            }

            $indexes[] = $index;
        }

        return $indexes;
    }

    /**
     * @return array
     */
    public function serialize () : array
    {
        $list = [];

        foreach ($this->markups as $tag => $index)
        {
            $list[] = [$tag];
        }

        return $list;
    }
}
