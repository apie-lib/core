<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;

class PhpDateIntervalString implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^P((\d+Y)?(\d+M)?(\d+D)?(T(\d+H)?(\d+M)?(\d+([.,]\d+)?S)?)?|(\d+W))$/';
    }
}
