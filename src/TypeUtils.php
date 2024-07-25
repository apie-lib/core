<?php
namespace Apie\Core;

use Apie\Core\Utils\ConverterUtils;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Throwable;

final class TypeUtils
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function allowEmptyString(
        ?ReflectionType $type
    ): bool {
        if ($type === null) {
            return true;
        }
        if ($type instanceof ReflectionNamedType) {
            if ($type->getName() === 'string' || $type->getName() === 'mixed') {
                return true;
            }
            $class = ConverterUtils::toReflectionClass($type);
            if (!$class) {
                return false;
            }
            if (in_array(ValueObjectInterface::class, $class->getInterfaceNames())) {
                try {
                    $class->getMethod('fromNative')->invoke(null, '');
                    return true;
                } catch (Throwable) {
                    return false;
                }
            }
            return false;
        }
        if ($type instanceof ReflectionIntersectionType) {
            foreach ($type->getTypes() as $type) {
                if (!self::allowEmptyString($type)) {
                    return false;
                }
            }
            return true;
        }
        assert($type instanceof ReflectionUnionType);
        foreach ($type->getTypes() as $type) {
            if (self::allowEmptyString($type)) {
                return true;
            }
        }
        return false;
    }

    public static function couldBeAStream(
        ?ReflectionType $type
    ): bool {
        if ($type === null) {
            return true;
        }
        if ($type instanceof ReflectionNamedType) {
            return in_array($type->getName(), ['mixed', 'resource', UploadedFileInterface::class]);
        }
        assert($type instanceof ReflectionIntersectionType || $type instanceof ReflectionUnionType);
    
        foreach ($type->getTypes() as $type) {
            if (self::couldBeAStream($type)) {
                return true;
            }
        }
        return false;
    }

    public static function matchesType(
        ?ReflectionType $type,
        mixed $input
    ): bool {
        if ($type === null) {
            return true;
        }
        if ($input === null && $type->allowsNull()) {
            return true;
        }
        if ($type instanceof ReflectionNamedType) {
            return match ($type->getName()) {
                'mixed' => true,
                'bool' => is_bool($input),
                'int' => is_int($input),
                'string' => is_string($input),
                'null' => $input === null,
                'true' => $input === true, // PHP 8.2
                'false' => $input === false,
                default => get_debug_type($input) === $type->getName()
                    || ((interface_exists($type->getName()) || class_exists($type->getName()))
                        && is_object($input)
                        && (new ReflectionClass($type->getName()))->isInstance($input))
            };
        }
        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $type) {
                if (self::matchesType($type, $input)) {
                    return true;
                }
            }
            return false;
        }
        assert($type instanceof ReflectionIntersectionType);
        foreach ($type->getTypes() as $type) {
            if (!self::matchesType($type, $input)) {
                return false;
            }
        }
        return true;
    }
}
