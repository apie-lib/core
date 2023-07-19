<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionType;

class IgnoredField implements PersistenceFieldInterface
{
    use IsPropertyField;

    public function isAllowsNull(): bool
    {
        return true;
    }
    public function getType(): ReflectionType
    {
        return $this->getProperty()->getType() ?? ReflectionTypeFactory::createReflectionType('mixed');
    }
    public function getPersistenceType(): PersistenceColumn
    {
        return PersistenceColumn::NULL;
    }
}
