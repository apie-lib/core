<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use ReflectionType;

class FieldInvariant implements PersistenceFieldInterface
{
    public function __construct(private readonly PersistenceFieldInterface $field)
    {
    }

    public function getDeclaredClass(): ?string
    {
        return $this->field->getDeclaredClass();
    }

    public function getName(): string
    {
        return 'invariant_' . $this->field->getName();
    }
    public function isAllowsNull(): bool
    {
        return $this->field->isAllowsNull();
    }
    public function getType(): ReflectionType
    {
        return $this->field->getType();
    }

    public function getDecoratedField(): PersistenceFieldInterface
    {
        return $this->field;
    }

    public function getPersistenceType(): PersistenceColumn
    {
        return $this->field->getPersistenceType();
    }
}
