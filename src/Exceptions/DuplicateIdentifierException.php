<?php
namespace Apie\Core\Exceptions;

use Apie\Core\ValueObjects\Utils;

/**
 * Exception thrown when a an identifier is already defined.
 */
final class DuplicateIdentifierException extends ApieException
{
    public function __construct(string $identifier, mixed $alreadyDefined, mixed $newValue)
    {
        parent::__construct(
            sprintf(
                'Duplicate identifier "%s"! Value already defined is "%s". New value defined is "%s"',
                $identifier,
                Utils::displayMixedAsString($alreadyDefined),
                Utils::displayMixedAsString($newValue)
            )
        );
    }
}
