<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\FakeMethod;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\Interfaces\LengthConstraintStringValueObjectInterface;
use Faker\Generator;

#[FakeMethod('createRandom')]
#[Description('A file name with extension, but without path')]
final class Filename implements HasRegexValueObjectInterface, LengthConstraintStringValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^(?=.{1,255}$)[^<>:"\/\\|?*\x00-\x1F\x7F]+(\.[^<>:"\/\\|?*\x00-\x1F\x7F]+)?$/';
    }

    public static function minStringLength(): int
    {
        return 1;
    }

    public static function maxStringLength(): int
    {
        return 255;
    }

    public static function createRandom(Generator $generator): static
    {
        return new static($generator->word() . '.' . $generator->fileExtension());
    }
}
