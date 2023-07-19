<?php
namespace Apie\Core\Utils;

use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
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
        return $class->name === ItemHashmap::class || $class->isSubclassOf(ItemHashmap::class);
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isList(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return $class->name === ItemList::class || $class->isSubclassOf(ItemList::class);
    }
}
