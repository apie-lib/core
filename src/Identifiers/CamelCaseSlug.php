<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\FakeMethod;
use Apie\Core\Utils\IdentifierConstants;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;
use Faker\Generator;

/**
 * Indicate camel case string for id's starting with a lower case
 */
#[FakeMethod('createRandom')]
#[Description('Any string in camel case with first letter being lower case, for example "exampleObject"')]
class CamelCaseSlug implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[a-z][a-zA-Z0-9]*$/';
    }

    public function humanize(): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $this->internal));
    }

    public function toKebabCaseSlug(): KebabCaseSlug
    {
        return new KebabCaseSlug(strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $this->internal)));
    }

    public function toPascalCaseSlug(): PascalCaseSlug
    {
        return new PascalCaseSlug(ucfirst($this->internal));
    }

    public function toSnakeCaseSlug(): SnakeCaseSlug
    {
        return new SnakeCaseSlug(strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $this->internal)));
    }

    public static function createRandom(Generator $faker): static
    {
        if ($faker->boolean()) {
            return static::fromNative($faker->randomElement(IdentifierConstants::RANDOM_IDENTIFIERS));
        }
        $words = $faker->words($faker->numberBetween(2, 3));
        $firstWord = array_shift($words);
        $words = array_map('ucfirst', $words);
        return static::fromNative($firstWord . implode('', $words));
    }
}
