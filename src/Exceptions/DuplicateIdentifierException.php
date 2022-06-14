<?php
namespace Apie\Core\Exceptions;

use Apie\Core\ValueObjects\Utils;
use ReflectionNamedType;
use ReflectionUnionType;
use Throwable;

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