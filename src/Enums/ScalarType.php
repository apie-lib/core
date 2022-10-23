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
    case BOOLEAN = 'boolean';
    case MIXED = 'mixed';
    case STDCLASS = stdClass::class;

    public function toReflectionType(): ReflectionType
    {
        return ReflectionTypeFactory::createReflectionType($this->value);
    }
}
