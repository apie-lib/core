<?php
namespace Apie\Core\TypeConverters;

use Apie\TypeConverter\ConverterInterface;
use ReflectionClass;

/**
 * @implements ConverterInterface<class-string<object>, ReflectionClass<object>>
 */
final class StringToReflectionClassConverter implements ConverterInterface
{
    /**
     * @template T of object
     * @param class-string<T> $input
     * @return ReflectionClass<T>
     */
    public function convert(string $input): ReflectionClass
    {
        return new ReflectionClass($input);
    }
}
