<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\FakeMethod;
use Apie\Core\Attributes\ProvideIndex;
use Apie\Core\ValueObjects\Concerns\IndexesWords;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Faker\Generator;

#[FakeMethod('createRandom')]
#[ProvideIndex('getIndexes')]
class NonEmptyString implements HasRegexValueObjectInterface
{
    use IndexesWords;
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^.+$/s';
    }

    protected function convert(string $input): string
    {
        return trim($input);
    }

    public static function createRandom(Generator $generator): self
    {
        return new static($generator->realText(1024));
    }
}
