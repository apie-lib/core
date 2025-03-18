<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\FakeMethod;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Faker\Generator;

#[FakeMethod('createRandom')]
final class StrictMimeType implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '~^[a-zA-Z0-9!#$&^_.+-]+/[a-zA-Z0-9!#$&^_.+-]+$~';
    }

    public static function createRandom(Generator $faker): static
    {
        return new static($faker->mimeType());
    }
}
