<?php
namespace Apie\Core\Exceptions;

class IndexNotFoundException extends ApieException
{
    public function __construct(string|int $index)
    {
        parent::__construct(
            sprintf(
                "Array contains no item with index '%s'",
                $index
            )
        );
    }
}
