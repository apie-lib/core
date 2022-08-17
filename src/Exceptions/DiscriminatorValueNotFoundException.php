<?php
namespace Apie\Core\Exceptions;

/**
 * Exception thrown when a discriminator requires a class,
 * but can not find the proper discriminator value.
 */
final class DiscriminatorValueException extends ApieException
{
    public function __construct(string $identifier)
    {
        parent::__construct(
            sprintf('Discriminator not found: "' . $identifier . '"')
        );
    }
}
