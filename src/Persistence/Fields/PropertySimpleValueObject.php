<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

final class PropertySimpleValueObject implements PersistenceFieldInterface
{
    use IsPropertyField;

    public function getType(): ReflectionType
    {
        $propertyType = $this->getProperty()->getType();
        assert($propertyType instanceof ReflectionNamedType);
        return (new ReflectionClass($propertyType->getName()))->getMethod('toNative')->getReturnType();
    }

    public function isAllowsNull(): bool
    {
        $propertyType = $this->getProperty()->getType();
        if ($propertyType->allowsNull()) {
            return true;
        }
        return $this->getType()->allowsNull();
    }

    public function getPersistenceType(): PersistenceColumn
    {
        return PersistenceColumn::createFromType($this->getType());
    }
}
