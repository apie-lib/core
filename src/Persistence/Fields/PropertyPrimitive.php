<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use ReflectionType;

final class PropertyPrimitive implements PersistenceFieldInterface
{
    use IsPropertyField;

    public function getType(): ReflectionType
    {
        return $this->getProperty()->getType();
    }

    public function isAllowsNull(): bool
    {
        $type = $this->getType();
        return $type->allowsNull();
    }

    public function getPersistenceType(): PersistenceColumn
    {
        return PersistenceColumn::createFromType($this->getType());
    }
}
