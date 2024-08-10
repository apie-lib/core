<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Utils\IdentifierConstants;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;
use Faker\Generator;

/**
 * Indicate an identifier as id.
 */
class Identifier implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[a-z][a-z0-9]*$/';
    }

    public static function createRandom(Generator $faker): static
    {
        if ($faker->boolean()) {
            return new static(strtolower($faker->randomElement(IdentifierConstants::RANDOM_IDENTIFIERS)));
        }
        return new static($faker->words($faker->numberBetween(1, 3), true));
    }
}
