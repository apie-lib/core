<?php
namespace Apie\Core\Utils;

use Apie\Core\ValueObjects\CompositeValueObject;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;

final class ValueObjectUtils
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param string|ReflectionClass<ValueObjectInterface>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function toNativeType(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): ReflectionType
    {
        $class = ConverterUtils::toReflectionClass($input);
        return ConverterUtils::toReflectionType($class->getMethod('toNative'));
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isValueObject(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        if (!$class || !$class->implementsInterface(ValueObjectInterface::class) || $class->isAbstract()) {
            return false;
        }
        return true;
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isNonCompositeValueObject(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        if (!self::isValueObject($class)) {
            return false;
        }

        if (in_array(CompositeValueObject::class, $class->getTraitNames())) {
            return false;
        }
        $parentClass = $class->getParentClass();

        return $parentClass ? self::isNonCompositeValueObject($parentClass) : true;
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isCompositeValueObject(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        if (!self::isValueObject($class)) {
            return false;
        }

        if (in_array(CompositeValueObject::class, $class->getTraitNames())) {
            return true;
        }
        $parentClass = $class->getParentClass();

        return $parentClass ? self::isCompositeValueObject($parentClass) : false;
    }
}
