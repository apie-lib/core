<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\FakeMethod;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;
use Faker\Generator;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Indicate an identifier written with underscores and lowercase only(pascal_case).
 */
#[FakeMethod('createRandom')]
#[Description('Lowercase text written with underscores for separate words, for example "example_object"')]
class SnakeCaseSlug implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[a-z0-9]+(_[a-z0-9]+)*$/';
    }

    /**
     * @param ReflectionClass<object>|ReflectionMethod|ReflectionProperty|string $class
     */
    public static function fromClass(ReflectionClass|ReflectionMethod|ReflectionProperty|string $class): self
    {
        if (is_object($class)) {
            $shortName = $class instanceof ReflectionClass ? $class->getShortName() : $class->name;
            $short = preg_replace('/([a-z])([A-Z])/', '$1_$2', $shortName);
        } else {
            $short = $class;
        }
        return static::fromNative(strtolower($short));
    }

    public function humanize(): string
    {
        return str_replace('_', ' ', $this->internal);
    }

    public function toCamelCaseSlug(): CamelCaseSlug
    {
        return new CamelCaseSlug(lcfirst(str_replace('_', '', ucwords($this->internal, '_'))));
    }

    public function toPascalCaseSlug(): PascalCaseSlug
    {
        return new PascalCaseSlug(str_replace('_', '', ucwords($this->internal, '_')));
    }

    public function toKebabCaseSlug(): KebabCaseSlug
    {
        return new KebabCaseSlug(str_replace('_', '-', $this->internal));
    }

    public static function createRandom(Generator $faker): static
    {
        return static::fromNative(CamelCaseSlug::createRandom($faker)->toSnakeCaseSlug()->toNative());
    }
}
