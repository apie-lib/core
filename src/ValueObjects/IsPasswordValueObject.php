<?php
namespace Apie\Core\ValueObjects;

trait IsPasswordValueObject
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        $lowercase = '(?=(.*[a-z]){' . self::getMinLowercase() . ',})';
        $uppercase = '(?=(.*[A-Z]){' . self::getMinUppercase() . ',})';
        $digits = '(?=(.*[0-9]){' . self::getMinDigits() . ',})';
        $specialCharactersRegex = str_replace('\#', '#', preg_quote(self::getAllowedSpecialCharacters(), '/'));
        $specialCharacter = '(?=(.*[' . $specialCharactersRegex . ']){' . self::getMinSpecialCharacters() . ',})';
        $totalSize = '[a-zA-Z0-9' . $specialCharactersRegex . ']{' . self::getMinLength() . ',' . self::getMaxLength() . '}';
        return '/^' . $lowercase . $uppercase . $digits . $specialCharacter . $totalSize . '$/';
    }

    abstract public static function getMinLength(): int;

    abstract public static function getMaxLength(): int;

    abstract public static function getAllowedSpecialCharacters(): string;

    abstract public static function getMinSpecialCharacters(): int;

    abstract public static function getMinDigits(): int;

    abstract public static function getMinLowercase(): int;

    abstract public static function getMinUppercase(): int;
}
