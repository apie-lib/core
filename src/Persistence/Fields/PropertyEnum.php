<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionEnum;
use ReflectionNamedType;
use ReflectionType;

final class PropertyEnum implements PersistenceFieldInterface
{
    use IsPropertyField;

    public function getType(): ReflectionType
    {
        $propertyType = $this->getProperty()->getType();
        assert($propertyType instanceof ReflectionNamedType);
        $enum = new ReflectionEnum($propertyType->getName());
        return $enum->getBackingType() ?? ReflectionTypeFactory::createReflectionType('string');
    }

    public function isAllowsNull(): bool
    {
        $propertyType = $this->getProperty()->getType();
        return $propertyType->allowsNull();
    }

    public function getPersistenceType(): PersistenceColumn
    {
        return PersistenceColumn::createFromType($this->getType());
    }
}
