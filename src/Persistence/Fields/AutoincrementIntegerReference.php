<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionType;

/**
 * Doctrine only supports auto-increment integers as ID, so if an other field is defined we need an extra table
 * to handle those autoincrement integers. This field is a reference to this table.
 */
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
        return $type->allowsNull();
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
