<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionType;

final class AutoincrementInteger implements PersistenceFieldInterface
{
    public function __construct(private readonly string $name = 'id')
    {
    }

    public function getName(): string
    {
        return $this->name;
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
