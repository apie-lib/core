<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use ReflectionType;

final class FieldReference implements PersistenceFieldInterface
{
    use IsPropertyField;

    public function __construct(
        private readonly string $declaredClass,
        private readonly string $propertyName,
        private readonly PersistenceFieldInterface $field,
        private readonly string $tableName
    ) {
    }

    public function getType(): ReflectionType
    {
        return $this->field->getType();
    }

    public function isAllowsNull(): bool
    {
        $propertyType = $this->getProperty()->getType();
        return !$propertyType || $propertyType->allowsNull();
    }

    public function getPersistenceType(): PersistenceColumn
    {
        return $this->field->getPersistenceType();
    }

    public function getTableReference(): string
    {
        return $this->tableName;
    }
}
