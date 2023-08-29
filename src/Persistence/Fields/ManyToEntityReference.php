<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\IdentifierUtils;
use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use ReflectionClass;
use ReflectionType;

class ManyToEntityReference implements PersistenceFieldInterface
{
    public function __construct(
        private readonly string $name,
        private readonly string $entityClassName,
        private readonly BoundedContextId $boundedContextId
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function getDeclaredClass(): ?string
    {
        return null;
    }
    public function isAllowsNull(): bool
    {
        return false;
    }

    public function getEntityReference(): string
    {
        return 'apie_entity_' . $this->boundedContextId . '_' . IdentifierUtils::classNameToUnderscore(new ReflectionClass($this->entityClassName));
    }

    public function getType(): ReflectionType
    {
        return (new ReflectionClass($this->entityClassName))->getMethod('getId')->getReturnType();
    }
    public function getPersistenceType(): PersistenceColumn
    {
        return PersistenceColumn::createFromType($this->getType());
    }
}
