<?php
namespace Apie\Core\Utils;

use Apie\Core\Dto\DtoInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;

final class DtoUtils
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param string|ReflectionClass<covariant object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isDto(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return $class->isInstantiable() && in_array(DtoInterface::class, $class->getInterfaceNames());
    }
}
