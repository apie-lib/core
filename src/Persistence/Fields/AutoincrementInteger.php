<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionType;

/**
 * Add an auto-increment integer field.
 */
final class AutoincrementInteger implements PersistenceFieldInterface
{
    public function __construct(private readonly string $name = 'id', private readonly bool $internal = true)
    {
    }

    public function isInternal(): bool
    {
        return $this->internal;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDeclaredClass(): ?string
    {
        return null;
    }

    public function isAllowsNull(): bool
    {
        return true;
    }
    public function getType(): ReflectionType
    {
        return ReflectionTypeFactory::createReflectionType('int');
    }
    public function getPersistenceType(): PersistenceColumn
    {
        return PersistenceColumn::INT;
    }
}
