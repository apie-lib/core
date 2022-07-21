<?php
namespace Apie\Core\Identifiers;

use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;

class PascalCaseSlug implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[a-z0-9]+(_[a-z0-9]+)*$/';
    }
}
