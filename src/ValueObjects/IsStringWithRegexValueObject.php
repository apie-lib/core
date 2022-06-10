<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use ReflectionClass;

trait IsStringWithRegexValueObject
{
    use IsStringValueObject;
    public static function validate(string $input): void
    {
        if (!preg_match(self::getRegularExpression(), $input)) {
            throw new InvalidStringForValueObjectException($input, new ReflectionClass(self::class));
        }
    }

    abstract public static function getRegularExpression(): string;
}
