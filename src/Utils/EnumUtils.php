<?php
namespace Apie\Core\Utils;

use ReflectionClass;
use ReflectionEnum;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;

final class EnumUtils
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @return array<int, string>
     */
    public static function getValues(ReflectionEnum $enumClass): array
    {
        $result = [];
        foreach ($enumClass->getCases() as $enumCase) {
            $result[$enumCase->getValue()->value ?? $enumCase->getValue()->name] = $enumCase->getValue()->name;
        }
        return $result;
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isEnum(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return $class->isInstantiable() && $class->isEnum();
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isStringEnum(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return self::isEnum($class) && $class instanceof ReflectionEnum && (!$class->getBackingType() || $class->getBackingType()->getName() === 'string');
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isIntEnum(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return self::isEnum($class) && $class instanceof ReflectionEnum && ($class->getBackingType() && $class->getBackingType()->getName() === 'int');
    }
}
