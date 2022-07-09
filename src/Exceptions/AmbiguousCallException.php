<?php
namespace Apie\Core\Exceptions;

class AmbiguousCallException extends ApieException
{
    public function __construct(string $identifier, string... $names)
    {
        parent::__construct(
            sprintf(
                "Ambiguous call for %s, could be either an instance of %s",
                $identifier,
                implode(', ', $names)
            )
        );
    }
}