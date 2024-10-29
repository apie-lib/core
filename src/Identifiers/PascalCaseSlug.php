<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Attributes\FakeMethod;
use Apie\Core\Utils\IdentifierConstants;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;
use Faker\Generator;

/**
 * Indicate camel case string starting with a capital (for example PascalCase)
 */
#[FakeMethod('createRandom')]
class PascalCaseSlug implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[A-Z][a-zA-Z0-9]*$/';
    }

    public function humanize(): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $this->internal));
    }

    public function toCamelCaseSlug(): CamelCaseSlug
    {
        return new CamelCaseSlug(lcfirst($this->internal));
    }

    public function toSnakeCaseSlug(): SnakeCaseSlug
    {
        return new SnakeCaseSlug(strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $this->internal)));
    }

    public function toKebabCaseSlug(): KebabCaseSlug
    {
        return new KebabCaseSlug(strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $this->internal)));
    }

    public static function createRandom(Generator $faker): static
    {
        if ($faker->boolean()) {
            return new static(ucfirst($faker->randomElement(IdentifierConstants::RANDOM_DOMAIN_OBJECT_NAMES)));
        }
        $words = $faker->words($faker->numberBetween(1, 3));
        $words = array_map('ucfirst', $words);
        return new static(implode('', $words));
    }
}
