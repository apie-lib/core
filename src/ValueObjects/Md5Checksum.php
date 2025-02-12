<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;

class Md5Checksum implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[a-f0-9]{32}$/';
    }

    protected function convert(string $input): string
    {
        return strtolower($input);
    }
}
