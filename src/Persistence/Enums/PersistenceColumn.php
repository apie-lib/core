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

    public function toType(): string
    {
        return match($this) {
            self::BLOB => 'array',
            self::SMALLTEXT => 'string',
            self::TEXT => 'string',
            self::INT => 'int',
            self::FLOAT => 'float',
            self::BOOLEAN => 'bool',
            self::STREAM => 'resource',
            self::NULL => '?int', // we still need php 8.1 support
        };
    }

    public function toDoctrineType(): string
    {
        return match($this) {
            self::BLOB => 'blob',
            self::SMALLTEXT => 'string',
            self::TEXT => 'text',
            self::INT => 'integer',
            self::FLOAT => 'float',
            self::BOOLEAN => 'boolean',
            self::STREAM => 'blob',
            self::NULL => 'boolean',
        };
    }

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
