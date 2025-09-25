<?php

namespace Apie\Core\ValueObjects;

use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;

abstract class Decimal implements ValueObjectInterface, HasRegexValueObjectInterface
{
    final protected function __construct(
        private int $integerPart,
        private string $decimalPart,
    ) {
    }

    final public function __toString(): string
    {
        return $this->integerPart . '.' . $this->decimalPart;
    }

    abstract static public function getNumberOfDecimals(): int;

    final public static function getRegularExpression(): string
    {
        return '/^-?([0-9]|[1-9][0-9]*)\.[0-9]{' .static::getNumberOfDecimals() . '}$/';
    }

    final public static function fromNative(mixed $input): static
    {
        $decimals = static::getNumberOfDecimals();
        if (is_numeric($input)) {
            $float = floatval($input);
            $formatted = number_format($float, $decimals, '.', '');
            [$int, $dec] = explode('.', $formatted);
            return new static((int)$int, $dec);
        }
        $string = trim(Utils::toString($input));
        // we do not use getRegularExpression here, because we are more lenient on fromNative.
        if (preg_match(
            '/^(?<int>-?([0-9]|[1-9][0-9]*))\.(?<part>[0-9]{0,' . $decimals . '})[0-9]*$/',
            $string,
            $matches
        )) {
            return new static((int)$matches['int'], str_pad($matches['part'], $decimals, '0', STR_PAD_RIGHT));
        }

        throw new InvalidStringForValueObjectException($input, new \ReflectionClass(static::class));
    }

    final public function toNative(): string
    {
        return $this->__toString();
    }

    final public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    final public function add(int|float|self ...$values): static
    {
        $sum = (float)$this->__toString();
        foreach ($values as $value) {
            $sum += (float) ($value instanceof self ? $value->__toString() : $value);
        }
        return static::fromNative($sum);
    }

    final public function subtract(int|float|self ...$values): static
    {
        $result = (float)$this->__toString();
        foreach ($values as $value) {
            $result -= (float) ($value instanceof self ? $value->__toString() : $value);
        }
        return static::fromNative($result);
    }

    final public function multiply(int|float|self ...$values): static
    {
        $result = (float)$this->__toString();
        foreach ($values as $value) {
            $result *= (float) ($value instanceof self ? $value->__toString() : $value);
        }
        return static::fromNative($result);
    }

    final public function divide(int|float|self ...$values): static
    {
        $result = (float)$this->__toString();
        foreach ($values as $value) {
            $divisor = (float) ($value instanceof self ? $value->__toString() : $value);
            if ($divisor == 0.0) {
                throw new \DivisionByZeroError("Division by zero");
            }
            $result /= $divisor;
        }
        return static::fromNative($result);
    }
}