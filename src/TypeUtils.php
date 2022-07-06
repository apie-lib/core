<?php
namespace Apie\Core;

use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

final class TypeUtils
{
    private function __construct()
    {
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
                'int' => is_int($input),
                'string' => is_string($input),
                'null' => $input === null,
                'true' => $input === true, // PHP 8.2
                'false' => $input === false,
                default => get_debug_type($input) === $type->getName()
                    || (interface_exists($type->getName())
                        && is_object($input)
                        && (new ReflectionClass($input))->implementsInterface($type->getName()))
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
