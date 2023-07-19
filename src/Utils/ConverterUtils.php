<?php
namespace Apie\Core\Utils;

use Apie\Core\TypeConverters\ReflectionMethodToReflectionClassConverter;
use Apie\Core\TypeConverters\ReflectionPropertyToReflectionClassConverter;
use Apie\Core\TypeConverters\ReflectionTypeToReflectionClassConverter;
use Apie\Core\TypeConverters\StringToReflectionClassConverter;
use Apie\TypeConverter\Converters\ObjectToObjectConverter;
use Apie\TypeConverter\DefaultConvertersFactory;
use Apie\TypeConverter\TypeConverter;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;

final class ConverterUtils
{
    private static self $instance;

    private TypeConverter $typeConverter;

    private function __construct()
    {
        $this->typeConverter = new TypeConverter(
            new ObjectToObjectConverter(),
            ...DefaultConvertersFactory::create(
                new StringToReflectionClassConverter(),
                new ReflectionMethodToReflectionClassConverter(),
                new ReflectionPropertyToReflectionClassConverter(),
                new ReflectionTypeToReflectionClassConverter(),
            )
        );
    }

    /**
     * @template T of object
     * @param string|ReflectionClass<T>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     * @return ReflectionClass<T>
     */
    public static function toReflectionClass(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input, bool $strict = false): ?ReflectionClass
    {
        if ($input instanceof ReflectionClass) {
            return $input;
        }
        return self::getInstance()->typeConverter->convertTo($input, $strict ? 'ReflectionClass' : '?ReflectionClass');
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function toReflectionType(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input, bool $strict = false): ?ReflectionType
    {
        if ($input instanceof ReflectionType) {
            return $input;
        }
        return self::getInstance()->typeConverter->convertTo($input, $strict ? 'ReflectionType' : '?ReflectionType');
    }

    private static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
