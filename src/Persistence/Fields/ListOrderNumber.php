<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionType;

class ListOrderNumber implements PersistenceFieldInterface
{
    public function getName(): string
    {
        return 'order';
    }

    public function getDeclaredClass(): ?string
    {
        return null;
    }

    public function isAllowsNull(): bool
    {
        return false;
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
