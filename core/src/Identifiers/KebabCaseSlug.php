<?php
namespace Apie\Core\Identifiers;

use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;

/**
 * Indicate an identifier written with dashes (kebab-case).
 */
class KebabCaseSlug implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[a-z0-9]+(\-[a-z0-9]+)*$/';
    }
}
