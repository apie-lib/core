<?php
namespace Apie\Core\Utils;

use Apie\Core\TypeConverters\ArrayToDoctrineCollection;
use Apie\Core\TypeConverters\DoctrineCollectionToArray;
use Apie\Core\TypeConverters\IntToAutoincrementIntegerConverter;
use Apie\Core\TypeConverters\ReflectionMethodToReflectionClassConverter;
use Apie\Core\TypeConverters\ReflectionPropertyToReflectionClassConverter;
use Apie\Core\TypeConverters\ReflectionTypeToReflectionClassConverter;
use Apie\Core\TypeConverters\ReflectionUnionTypeToReflectionClassConverter;
use Apie\Core\TypeConverters\StringToReflectionClassConverter;
use Apie\StorageMetadataBuilder\Interfaces\MixedStorageInterface;
use Apie\TypeConverter\Converters\ObjectToObjectConverter;
use Apie\TypeConverter\DefaultConvertersFactory;
use Apie\TypeConverter\TypeConverter;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
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
        ];
        $this->typeConverter = new TypeConverter(
            new ObjectToObjectConverter(),
            ...DefaultConvertersFactory::create(
                ...$converters
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

    /**
     * @return resource|null
     */
    public static function extractResourceFromStream(StreamInterface $uploadedFile): mixed
    {
        if ($uploadedFile instanceof Stream) {
            $refl = new ReflectionProperty(Stream::class, 'stream');
            return $refl->getValue($uploadedFile);
        }
        return null;
    }
}
