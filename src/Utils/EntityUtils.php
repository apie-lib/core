<?php
namespace Apie\Core\Utils;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Entities\PolymorphicEntityInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;

final class EntityUtils
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isEntity(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return $class->implementsInterface(EntityInterface::class)
            && !$class->isInterface();
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isNonPolymorphicEntity(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return !$class->implementsInterface(PolymorphicEntityInterface::class)
            && $class->implementsInterface(EntityInterface::class)
            && !$class->isInterface();
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isPolymorphicEntity(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return $class->implementsInterface(PolymorphicEntityInterface::class)
            && !$class->isInterface();
    }
}
