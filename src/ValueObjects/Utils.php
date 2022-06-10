<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Exceptions\InvalidTypeException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionUnionType;
use stdClass;
use Stringable;
use UnitEnum;

final class Utils
{
    private function __construct()
    {
    }

    public static function toArray(mixed $input): array
    {
        if (is_iterable($input)) {
            return iterator_to_array($input);
        }
        throw new InvalidTypeException($input, 'array');
    }

    public static function toString(mixed $input): string
    {
        return (string) $input;
    }

    public static function toInt(mixed $input): int
    {
        return (int) $input;
    }

    public static function toFloat(mixed $input): float
    {
        return (float) $input;
    }

    public static function toDate(mixed $input, string $class = DateTimeImmutable::class): DateTimeInterface
    {
        if ($class === DateTimeInterface::class) {
            $class = DateTimeImmutable::class;
        }
        if ($input instanceof DateTimeInterface) {
            return $class::createFromInterface(DateTime::ATOM, $input);
        }
        // TODO: date value objects.
        return $class::createFromFormat(DateTime::ATOM, self::toString($input));
    }

    public static function toNative(mixed $input): array|string|int|float|bool|UnitEnum
    {
        if ($input instanceof ValueObjectInterface) {
            $input = $input->toNative();
        }
        if ($input instanceof stdClass) {
            $input = json_decode(json_encode($input), true);
        }
        if (is_iterable($input)) {
            $result = [];
            foreach ($input as $key => $value) {
                $result[$key] = self::toNative($value);
            }
            return $result;
        }
        // TODO PHP 8.1 enum
        if (is_object($input) && $input instanceof Stringable) {
            return (string) $input;
        }
        if (is_string($input) || is_numeric($input)) {
            return $input;
        }
        throw new InvalidTypeException($input, 'ValueObject|array|string|int|float|bool|Enum');
    }

    /**
     * Converts native value in typehint.
     */
    public static function toTypehint(
        ReflectionUnionType|ReflectionNamedType $typehint,
        mixed $input
    ): mixed {
        if ($input === null) {
            if ($typehint->allowsNull()) {
                return null;
            }
            throw InvalidTypeException::fromTypehint($input, $typehint);
        }
        $types = $typehint instanceof ReflectionUnionType ? $typehint->getTypes() : [$typehint];
        $lastError = new InvalidTypeException($input, '(unknown)');
        foreach ($types as $type) {
            try {
                if ($type->isBuiltin()) {
                    switch ($type->getName()) {
                        case 'string':
                            return self::toString($input);
                        case 'int':
                            return self::toInt($input);
                        case 'float':
                            return self::toFloat($input);
                        case 'array':
                        case 'iterable':
                            return self::toArray($input);
                        default:
                            throw new InvalidTypeException($input, $type->getName());
                    }
                }
                $className = $type->getName();
                switch ($className) {
                    case stdClass::class:
                        return json_decode(json_encode(self::toArray($input)), false);
                    case DateTimeInterface::class:
                    case DateTimeImmutable::class:
                    case DateTime::class:
                        return self::toDate($input, $className);
                }
                $refl = new ReflectionClass($className);
                if ($refl->implementsInterface(ValueObjectInterface::class)) {
                    return $className::fromNative($input);
                }
                // TODO: PHP 8.1 enums
                throw new InvalidTypeException($className, 'ValueObjectInterface');
            } catch (InvalidTypeException $error) {
                $lastError = $error;
            }
        }
        throw InvalidTypeException::chainException($lastError);
    }

    public static function displayMixedAsString(mixed $input): string
    {
        if ($input === null) {
            return '(null)';
        }
        if (is_object($input)) {
            if ($input instanceof Stringable) {
                return '(object "' . $input->__toString() . '")';
            }
            if ($input instanceof DateTimeInterface) {
                return $input->format(DateTime::ATOM);
            }
            return '(object ' . self::getDisplayNameForValueObject(new ReflectionClass($input)) . ')';
        }

        if (is_bool($input)) {
            return json_encode($input);
        }

        if (is_string($input) || is_numeric($input)) {
            return (string) $input;
        }

        return gettype($input);
    }

    public static function getDisplayNameForValueObject(ValueObjectInterface|ReflectionClass $class): string
    {
        if ($class instanceof ReflectionClass) {
            $className = $class->getShortName();
        } else {
            $className = (new ReflectionClass($class))->getShortName();
        }
        if (strcasecmp($className, 'Abstract') === 0 || strcasecmp($className, 'AbstractInterface') === 0) {
            return 'Abstract';
        }
        return preg_replace(
            '/Interface/i',
            '',
            preg_replace('/^abstract/i', '', $className)
        );
    }
}
