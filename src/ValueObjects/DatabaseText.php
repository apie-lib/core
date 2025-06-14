<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\FakeMethod;
use Apie\Core\Attributes\ProvideIndex;
use Apie\Core\Attributes\SchemaMethod;
use Apie\Core\ValueObjects\Concerns\IndexesWords;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Faker\Generator;

#[FakeMethod('createRandom')]
#[SchemaMethod('createSchema')]
#[ProvideIndex('getIndexes')]
#[Description('Any text with a maximum length of 65535 characters that fit in a TEXT field in a database')]
final class DatabaseText implements HasRegexValueObjectInterface
{
    use IndexesWords;
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^.{0,65535}$/s';
    }

    protected function convert(string $input): string
    {
        return trim($input);
    }

    public static function createRandom(Generator $generator): self
    {
        return new DatabaseText($generator->realText(1024));
    }

    /**
     * Provide OpenAPI schema. This is overwritten as some libraries will try to generate strings of 65535 characters
     * all the time for example strings resulting in terrible performance.
     *
     * @return array<string, string|int>
     */
    public static function createSchema(): array
    {
        return [
            'type' => 'string',
            'minLength' => 0,
            'maxLength' => 65535,
            'example' => 'Lorem Ipsum',
        ];
    }
}
