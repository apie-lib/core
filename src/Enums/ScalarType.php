<?php
namespace Apie\Core\Enums;

use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use stdClass;

enum ScalarType: string
{
    case STRING = 'string';
    case FLOAT = 'float';
    case INTEGER = 'int';
    case NULLVALUE = 'null';
    case ARRAY = 'array';
    case BOOLEAN = 'bool';
    case MIXED = 'mixed';
    case STDCLASS = stdClass::class;

    public const PRIMITIVES = [self::STRING, self::FLOAT, self::INTEGER, self::BOOLEAN];

    public static function createFromReflectionType(?ReflectionType $type, bool $ignoreNull): ScalarType
    {
        if ($type === null) {
            return self::MIXED;
        }
        if ($type instanceof ReflectionIntersectionType) {
            return self::STDCLASS;
        }
        if ($type instanceof ReflectionUnionType) {
            $current = null;
            foreach ($type->getTypes() as $subType) {
                $subScalar = self::createFromReflectionType($subType, $ignoreNull);
                if ($current === null) {
                    if ($subScalar === self::NULLVALUE && $ignoreNull) {
                        continue;
                    }
                    $current = $subScalar;
                } elseif ($current !== $subScalar) {
                    return self::MIXED;
                }
            }
            return $current ?? self::NULLVALUE;
        }
        assert($type instanceof ReflectionNamedType);

        return self::tryFrom($type->getName()) ?? self::MIXED;
    }

    public function toReflectionType(): ReflectionType
    {
        return ReflectionTypeFactory::createReflectionType($this->value);
    }

    public function toDoctrineType(): string
    {
        if ($this === self::INTEGER) {
            return 'integer';
        }
        return $this->value;
    }

    public function toJsonSchemaType(): string
    {
        if ($this === self::INTEGER) {
            return 'integer';
        }
        if ($this === self::FLOAT) {
            return 'number';
        }
        if ($this === self::BOOLEAN) {
            return 'boolean';
        }
        if ($this === self::STDCLASS) {
            return 'object';
        }
        return $this->value;
    }
}
