<?php
namespace Apie\Core\Exceptions;

final class IndexNotFoundException extends ApieException
{
    public function __construct(string|int|null $index)
    {
        parent::__construct(
            sprintf(
                "Array contains no item with index '%s'",
                $index === null ? '(null)' : $index
            )
        );
    }
}
