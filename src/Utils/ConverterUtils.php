<?php
namespace Apie\Core\Utils;

use Apie\Core\TypeConverters\ArrayToDoctrineCollection;
use Apie\Core\TypeConverters\DoctrineCollectionToArray;
use Apie\Core\TypeConverters\IntToAutoincrementIntegerConverter;
use Apie\Core\TypeConverters\ReflectionClassToReflectionTypeConverter;
use Apie\Core\TypeConverters\ReflectionMethodToReflectionClassConverter;
use Apie\Core\TypeConverters\ReflectionPropertyToReflectionClassConverter;
use Apie\Core\TypeConverters\ReflectionTypeToReflectionClassConverter;
use Apie\Core\TypeConverters\ReflectionUnionTypeToReflectionClassConverter;
use Apie\Core\TypeConverters\StringToReflectionClassConverter;
use Apie\StorageMetadataBuilder\Interfaces\MixedStorageInterface;
use Apie\TypeConverter\Converters\ObjectToObjectConverter;
use Apie\TypeConverter\DefaultConvertersFactory;
use Apie\TypeConverter\TypeConverter;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;

final class ConverterUtils
{
    private static self $instance;

    private TypeConverter $typeConverter;

    private function __construct()
    {
        $converters = [
            new ArrayToDoctrineCollection(),
            new DoctrineCollectionToArray(),
            new IntToAutoincrementIntegerConverter(),
            new StringToReflectionClassConverter(),
            new ReflectionMethodToReflectionClassConverter(),
            new ReflectionPropertyToReflectionClassConverter(),
            new ReflectionTypeToReflectionClassConverter(),
            new ReflectionUnionTypeToReflectionClassConverter(),
            new ReflectionClassToReflectionTypeConverter(),
        ];
        $this->typeConverter = new TypeConverter(
            new ObjectToObjectConverter(),
            ...DefaultConvertersFactory::create(
                ...$converters
            )
        );
    }

    /**
     * @param string|ReflectionClass<covariant object>|ReflectionProperty|ReflectionType|ReflectionMethod|null $input
     */
    public static function isStaticOrSelf(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod|null $input): bool
    {
        if (is_string($input)) {
            return $input === 'self' || $input === 'static';
        }
        if ($input instanceof ReflectionProperty) {
            return self::isStaticOrSelf($input->getType());
        }
        if ($input instanceof ReflectionNamedType) {
            return self::isStaticOrSelf($input->getName());
        }
        if ($input instanceof \ReflectionIntersectionType || $input instanceof \ReflectionUnionType) {
            return false;
        }
        if ($input instanceof ReflectionType) {
            return self::isStaticOrSelf((string) $input);
        }
        return false;
    }

    /**
     * @template T of object
     * @param string|ReflectionClass<T>|ReflectionProperty|ReflectionType|ReflectionMethod|null $input
     * @param ReflectionMethod|ReflectionClass<T>|null $context
     * @return ReflectionClass<T>|null
     */
    public static function toReflectionClass(
        string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod|null $input,
        bool $strict = false,
        ReflectionMethod|ReflectionClass|null $context = null
    ): ?ReflectionClass {
        if ($input === null) {
            return null;
        }
        if ($input instanceof ReflectionClass) {
            return $input;
        }
        if (self::isStaticOrSelf($input)) {
            if ($context instanceof ReflectionMethod) {
                return $context->getDeclaringClass();
            }
            if ($context instanceof ReflectionClass) {
                return $context;
            }
            if ($input instanceof ReflectionMethod) {
                return $input->getDeclaringClass();
            }
            throw new \LogicException('I can not handle static without a proper context of usage');
        }
        if (is_string($input) && !class_exists($input)) {
            return null;
        }
        return self::getInstance()->typeConverter->convertTo($input, $strict ? 'ReflectionClass' : '?ReflectionClass');
    }

    /**
     * @param string|ReflectionClass<covariant object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function toReflectionType(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input, bool $strict = false): ?ReflectionType
    {
        if ($input instanceof ReflectionType) {
            return $input;
        }
        return self::getInstance()->typeConverter->convertTo($input, $strict ? 'ReflectionType' : '?ReflectionType');
    }

    public static function dynamicCast(mixed $input, ReflectionType $wantedType): mixed
    {
        if ($input instanceof MixedStorageInterface) {
            $input = $input->toOriginalObject();
        }
        if ($input === null && $wantedType->allowsNull()) {
            return null;
        }
        if (is_object($input)) {
            $class = self::toReflectionClass($wantedType);
            if ($class->isInstance($input)) {
                return $input;
            }
        } elseif ($wantedType instanceof ReflectionNamedType && $wantedType->getName() === get_debug_type($input)) {
            return $input;
        }
        return self::getInstance()->typeConverter->convertTo($input, $wantedType);
    }

    private static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
