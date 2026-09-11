<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;

class SingleLetter implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[a-z]$/';
    }
}
