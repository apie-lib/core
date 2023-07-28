<?php
namespace Apie\Core\Persistence\Metadata;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\IdentifierUtils;
use Apie\Core\Persistence\Fields\AutoincrementInteger;
use Apie\Core\Persistence\Lists\PersistenceFieldList;
use Apie\Core\Persistence\PersistenceTableInterface;
use ReflectionClass;

final class EntityAutoincrementMetadata implements PersistenceTableInterface
{
    /**
     * @param class-string<EntityInterface> $class
     */
    public function __construct(
        private readonly BoundedContextId $boundedContextId,
        private readonly string $class,
        private readonly string $propertyName
    ) {
    }

    public function getOriginalClass(): ?string
    {
        return $this->class;
    }

    public function getName(): string
    {
        return 'apie_entity_' . $this->boundedContextId . '_' . IdentifierUtils::classNameToUnderscore(new ReflectionClass($this->class)) . '___fk__'  . $this->propertyName;
    }

    public function getFields(): PersistenceFieldList
    {
        return new PersistenceFieldList([
            new AutoincrementInteger()
        ]);
    }
}
