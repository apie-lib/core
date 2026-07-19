<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\Attributes\FakeMethod;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Interfaces\StringValueObjectInterface;
use Faker\Generator;

#[Description('URI in RFC 3986 format.')]
#[FakeMethod('createRandom')]
#[ExampleValue('https://apie-lib.blogspot.com/')]
class Uri implements StringValueObjectInterface
{
    use IsStringValueObject;

    public static function createRandom(Generator $faker): Uri
    {
        return new self($faker->url());
    }

    public static function validate(string $input): void
    {
        if (!filter_var($input, FILTER_VALIDATE_URL)) {
            throw new InvalidStringForValueObjectException(
                $input,
                new \ReflectionClass(static::class)
            );
        }
        if (!self::isAllowedUrl($input)) {
            throw new InvalidStringForValueObjectException(
                $input,
                new \ReflectionClass(static::class)
            );
        }
    }

    protected static function isAllowedUrl(string $input): bool
    {
        return true;
    }
}
