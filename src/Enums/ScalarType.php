<?php
namespace Apie\Core\Enums;

use Apie\Core\ReflectionTypeFactory;
use ReflectionType;
use stdClass;

enum ScalarType: string
{
    case STRING = 'string';
    case FLOAT = 'float';
    case INTEGER = 'int';
    case NULL = 'null';
    case ARRAY = 'array';
    case BOOLEAN = 'bool';
    case MIXED = 'mixed';
    case STDCLASS = stdClass::class;

    public function toReflectionType(): ReflectionType
    {
        return ReflectionTypeFactory::createReflectionType($this->value);
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
