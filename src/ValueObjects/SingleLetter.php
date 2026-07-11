<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\ValueObjects\Interfaces\StringValueObjectInterface;

class SingleLetter implements StringValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[a-z]$/';
    }
}
