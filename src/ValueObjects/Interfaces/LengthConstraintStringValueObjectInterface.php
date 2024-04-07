<?php
namespace Apie\Core\ValueObjects\Interfaces;

/**
 * Add this interface to indicate a minimum and/or maximum string size. This metadata can be
 * used for example for picking a different strategy for string or knowing if an empty string
 * is valid data.
 */
interface LengthConstraintStringValueObjectInterface extends StringValueObjectInterface
{
    public static function minStringLength(): int;

    public static function maxStringLength(): ?int;
}