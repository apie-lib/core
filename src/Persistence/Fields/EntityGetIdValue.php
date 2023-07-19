<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use ReflectionClass;
use ReflectionType;

final class EntityGetIdValue implements PersistenceFieldInterface
{
    /**
     * @param class-string<EntityInterface> $class
     */
    public function __construct(private readonly string $class)
    {
    }

    public function getName(): string
    {
        return 'id';
    }

    public function isAllowsNull(): bool
    {
        return false;
    }

    public function getType(): ReflectionType
    {
        return (new ReflectionClass($this->class))->getMethod('getId')->getReturnType();
    }
    public function getPersistenceType(): PersistenceColumn
    {
        return PersistenceColumn::createFromType($this->getType());
    }
}
