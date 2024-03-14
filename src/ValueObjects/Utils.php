<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
use Apie\Core\ValueObjects\Interfaces\TimeRelatedValueObjectInterface;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use BackedEnum;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionUnionType;
use stdClass;
use Stringable;
use UnitEnum;

/**
 * Util classes used by or from value objects.
 */
final class Utils
{
    private function __construct()
    {
    }

    /**
     * @return mixed[]
     */
    public static function toArray(mixed $input): array
    {
        if (is_array($input)) {
            return $input;
        }
        if ($input instanceof ItemList || $input instanceof ItemHashmap) {
            return $input->toArray();
        }
        if (is_iterable($input)) {
            return iterator_to_array($input);
        }
        throw new InvalidTypeException($input, 'array');
    }

    public static function toString(mixed $input): string
    {
        if (! $input instanceof Stringable && $input instanceof BackedEnum) {
            return (string) $input->value;
        }
        if ($input instanceof DateTimeInterface) {
            return $input->format(DateTimeInterface::ATOM);
        }
        return (string) $input;
    }

    public static function toInt(mixed $input): int
    {
        if ($input instanceof ValueObjectInterface) {
            $input = $input->toNative();
        }
        $iInput = (int) $input;
        $sInput = (string) $input;
        if ($sInput !== ((string) $iInput)) {
            throw new InvalidTypeException(
                $input,
                'int'
            );
        }
        return (int) $input;
    }

    public static function toFloat(mixed $input): float
    {
        if ($input instanceof ValueObjectInterface) {
            $input = $input->toNative();
        }
        $inputString = trim(self::toString($input));
        if (!preg_match('/^[+-]?(?:\d+(?:\.\d*)?|\.\d+)(?:[eE][+-]?\d+)?$/', $inputString)) {
            throw new InvalidTypeException(
                $inputString,
                'float'
            );
        }
        return (float) $input;
    }

    public static function toBoolean(mixed $input): bool
    {
        if ($input instanceof ValueObjectInterface) {
            $input = $input->toNative();
        }
        return (bool) $input;
    }
    
    public static function toMixed(mixed $input): mixed
    {
        return $input instanceof ValueObjectInterface ? $input->toNative() : $input;
    }

    public static function toDate(mixed $input, string $class = DateTimeImmutable::class): DateTimeInterface
    {
        if ($class === DateTimeInterface::class) {
            $class = DateTimeImmutable::class;
        }
        if ($input instanceof DateTimeInterface) {
            return $class::createFromInterface($input);
        }
        if ($input instanceof TimeRelatedValueObjectInterface) {
            return $input->toDate();
        }
        return $class::createFromFormat(DateTime::ATOM, self::toString($input));
    }

    /**
     * @return mixed[]|string|int|float|bool|UnitEnum|null
     */
    public static function toNative(mixed $input): array|string|int|float|bool|UnitEnum|null
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
        if ($input instanceof UnitEnum) {
            return $input;
        }
        if (is_object($input) && $input instanceof Stringable) {
            return (string) $input;
        }
        if (is_string($input) || is_numeric($input)) {
            return $input;
        }
        if (null === $input) {
            return null;
        }
        throw new InvalidTypeException($input, 'ValueObject|array|string|int|float|bool|UnitEnum|null');
    }

    public static function toEnum(string $className, mixed $input): UnitEnum
    {
        if ($input instanceof $className) {
            return $input;
        }

        $input = self::toString($input);
        foreach ($className::cases() as $enum) {
            if ($enum->value === $input || $enum->name === $input) {
                return $enum;
            }
        }
        throw new InvalidTypeException($input, self::getDisplayNameForValueObject(new ReflectionClass($className)));
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
        $types = $typehint instanceof ReflectionUnionType ? self::sortTypes($input, ...$typehint->getTypes()) : [$typehint];
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
                        case 'bool':
                            return self::toBoolean($input);
                        case 'mixed':
                            return self::toMixed($input);
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
                if ($refl->implementsInterface(UnitEnum::class)) {
                    return self::toEnum($className, $input);
                }
                throw new InvalidTypeException($className, 'ValueObjectInterface');
            } catch (InvalidTypeException $error) {
                $lastError = $error;
            }
        }
        throw InvalidTypeException::chainException($lastError);
    }

    /**
     * Sort typehints in a specific order.
     *
     * @return array<int, ReflectionNamedType>
     */
    public static function sortTypes(mixed $input, ReflectionNamedType... $types): array
    {
        $prio = [
            'string' => gettype($input) === 'string' ?  -3 : 1,
            'int' => 0,
            'float' => -1,
        ];
        $callback = function (ReflectionNamedType $type1, ReflectionNamedType $type2) use (&$prio) : int {
            $name1 = $type1->getName();
            $name2 = $type2->getName();
            $prio1 = $prio[$name1] ?? -2;
            $prio2 = $prio[$name2] ?? -2;
            return $prio1 <=> $prio2;
        };

        usort($types, $callback);
        return $types;
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
            return (string) '"' . $input . '"';
        }

        return gettype($input);
    }

    /**
     * @param ValueObjectInterface|ReflectionClass<object> $class
     */
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
