<?php declare(strict_types=1);

namespace Becklyn\Mobiledoc\Exception;

class DuplicateExtensionMobiledocException extends MobiledocException
{
    /**
     */
    public function __construct (string $name, ?\Throwable $previous = null)
    {
        parent::__construct(
            \sprintf("Can't register multiple extensions with same name '%s'.", $name),
            $previous
        );
    }
}
