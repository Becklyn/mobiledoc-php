<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Mobiledoc\Structure\Section;

use Becklyn\Mobiledoc\Mobiledoc\Structure\ContentElement;


interface Section extends ContentElement
{
    /**
     * Returns whether the section is empty
     *
     * @return bool
     */
    public function isEmpty () : bool;
}
