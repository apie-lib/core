<?php
namespace Apie\Core\ValueObjects\Interfaces;

/**
 * Interface for value objects that can be expressed in a regular expression.
 */
interface HasRegexValueObjectInterface extends StringValueObjectInterface
{
    public static function getRegularExpression(): string;
}
