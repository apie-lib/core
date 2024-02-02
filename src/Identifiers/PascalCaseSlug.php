<?php
namespace Apie\Core\Identifiers;

use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;

/**
 * Indicate camel case string starting with a capital (for example PascalCase)
 */
class PascalCaseSlug implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[A-Z][a-zA-Z0-9]*$/';
    }

    public function toCamelCaseSlug(): CamelCaseSlug
    {
        return new CamelCaseSlug(lcfirst($this->internal));
    }

    public function toSnakeCaseSlug(): SnakeCaseSlug
    {
        return new SnakeCaseSlug(strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $this->internal)));
    }

    public function toKebabCaseSlug(): KebabCaseSlug
    {
        return new KebabCaseSlug(strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $this->internal)));
    }
}
