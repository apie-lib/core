<?php
namespace Apie\Core\Identifiers;

use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;
use ReflectionClass;
use ReflectionMethod;

/**
 * Indicate an identifier written with dashes (kebab-case).
 */
class KebabCaseSlug implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    /**
     * @param ReflectionClass<object>|ReflectionMethod $class
     */
    public static function fromClass(ReflectionClass|ReflectionMethod $class): self
    {
        $shortName = $class instanceof ReflectionClass ? $class->getShortName() : $class->name;
        $short = preg_replace('/([a-z])([A-Z])/', '$1-$2', $shortName);
        return static::fromNative(strtolower($short));
    }

    public static function getRegularExpression(): string
    {
        return '/^[a-z0-9]+(\-[a-z0-9]+)*$/';
    }
}
