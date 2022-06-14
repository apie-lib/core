<?php
namespace Apie\Core\Exceptions;

/**
 * Exception thrown when a an identifier is already defined.
 */
class DuplicateIdentifierException extends ApieException
{
    public function __construct(string $identifier)
    {
        parent::__construct(
            sprintf('Duplicate identifier "' . $identifier . '"')
        );
    }
}
