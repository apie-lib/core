<?php
namespace Apie\Core\Persistence\Enums;

use LogicException;
use ReflectionNamedType;
use ReflectionType;

enum PersistenceColumn: string
{
    case BLOB = 'blob';
    case SMALLTEXT = 'varchar';
    case TEXT = 'text';
    case INT = 'int';
    case FLOAT = 'float';
    case BOOLEAN = 'boolean';
    case STREAM = 'stream';
    case NULL = 'null';

    public static function createFromType(ReflectionType $type): PersistenceColumn
    {
        if ($type instanceof ReflectionNamedType) {
            if ($type->isBuiltin()) {
                return match($type->getName()) {
                    'string' => self::TEXT,
                    'int' => self::INT,
                    'float' => self::FLOAT,
                    'bool' => self::BOOLEAN,
                    'resource' => self::STREAM,
                    'null' => self::NULL,
                    'array' => self::BLOB,
                    default => throw new LogicException('Unknown type: ' . $type->getName()),
                };
            }
        }

        return self::BLOB;
    }
}
