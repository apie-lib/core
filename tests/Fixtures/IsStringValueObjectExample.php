<?php
namespace Apie\Tests\Core\Fixtures;

use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\IsStringValueObject;
use Apie\Core\ValueObjects\ValueObjectInterface;
use ReflectionClass;

class IsStringValueObjectExample implements ValueObjectInterface
{
    use IsStringValueObject;

    protected function convert(string $input): string
    {
        return trim($input);
    }

    public static function validate(string $input): void
    {
        if (empty($input)) {
            throw new InvalidStringForValueObjectException($input, new ReflectionClass(self::class));
        }
    }
}
