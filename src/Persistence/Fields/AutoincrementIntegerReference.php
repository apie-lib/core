<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionType;

final class AutoincrementIntegerReference implements PersistenceFieldInterface
{
    use IsPropertyField;

    public function __construct(
        private readonly string $declaredClass,
        private readonly string $propertyName,
        private readonly string $tableName
    ) {
    }

    public function isAllowsNull(): bool
    {
        $type = $this->getType();
        return $type ? $type->allowsNull() : true;
    }

    public function getType(): ReflectionType
    {
        return $this->getProperty()->getType() ?? ReflectionTypeFactory::createReflectionType('mixed');
    }

    public function getPersistenceType(): PersistenceColumn
    {
        return PersistenceColumn::createFromType($this->getType());
    }

    public function getTableReference(): string
    {
        return $this->tableName;
    }
}