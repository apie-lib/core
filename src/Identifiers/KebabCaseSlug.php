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
 * Indicate an identifier written with dashes (kebab-case).
 */
#[FakeMethod('createRandom')]
#[Description('lowercase text written in kebab case, for example "example-object"')]
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
            $shortName = preg_replace('/^__/', 'magicMethod', $shortName);
            $short = preg_replace('/([a-z])([A-Z])/', '$1-$2', $shortName);
        } else {
            $short = $class;
        }
        return static::fromNative(strtolower($short));
    }

    public function humanize(): string
    {
        return str_replace('-', ' ', $this->internal);
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

    public static function createRandom(Generator $faker): static
    {
        return static::fromNative(CamelCaseSlug::createRandom($faker)->toKebabCaseSlug()->toNative());
    }
}
