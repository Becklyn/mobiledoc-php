<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Renderer\Markup;


interface MarkupAttributesVisitor
{
    /**
     * Transforms the given markup transforms.
     *
     * Must return the transformed attributes or null.
     *   - If `null` is returned, the visitor indicates that it isn't responsible for transforming the given attributes.
     *   - If an array is returned, the array must contain the transformed attributes. No other visitor is visited after
     *     visitor did return a value.
     *
     * The $attributes array is a structured map of [key => value].
     * If an array is returned, it has to have the exact same structure.
     *
     * @param string $tagName
     * @param array  $attributes
     * @return array|null
     */
    public function transform (string $tagName, array $attributes) : ?array;
}
