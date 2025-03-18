<?php
namespace Apie\Core\Utils;

use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
use Apie\Core\Lists\ItemSet;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;

final class HashmapUtils
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
    public static function isHashmap(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return $class !== null && ($class->name === ItemHashmap::class || $class->isSubclassOf(ItemHashmap::class));
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function getArrayType(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): ReflectionType
    {
        $class = ConverterUtils::toReflectionClass($input);
        if ($class === null) {
            return ReflectionTypeFactory::createReflectionType('mixed');
        }
        return $class->getMethod('offsetGet')->getReturnType() ?? ReflectionTypeFactory::createReflectionType('mixed');
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isList(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return $class !== null && ($class->name === ItemList::class || $class->isSubclassOf(ItemList::class));
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isSet(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return $class !== null && ($class->name === ItemSet::class || $class->isSubclassOf(ItemSet::class));
    }
}
