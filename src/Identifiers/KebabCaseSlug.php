<?php
namespace Apie\Core\Identifiers;

use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Indicate an identifier written with dashes (kebab-case).
 */
class KebabCaseSlug implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    /**
     * @param ReflectionClass<object>|ReflectionMethod|ReflectionProperty|string $class
     */
    public static function fromClass(ReflectionClass|ReflectionMethod|ReflectionProperty|string $class): self
    {
        if (is_object($class)) {
            $shortName = $class instanceof ReflectionClass ? $class->getShortName() : $class->name;
            $short = preg_replace('/([a-z])([A-Z])/', '$1-$2', $shortName);
        } else {
            $short = $class;
        }
        return static::fromNative(strtolower($short));
    }

    public static function getRegularExpression(): string
    {
        return '/^[a-z0-9]+(\-[a-z0-9]+)*$/';
    }

    public function toCamelCaseSlug(): CamelCaseSlug
    {
        return new CamelCaseSlug(lcfirst(str_replace('-', '', ucwords($this->internal, '-'))));
    }

    public function toPascalCaseSlug(): PascalCaseSlug
    {
        return new PascalCaseSlug(str_replace('-', '', ucwords($this->internal, '-')));
    }

    public function toSnakeCaseSlug(): SnakeCaseSlug
    {
        return new SnakeCaseSlug(str_replace('-', '_', $this->internal));
    }
}
