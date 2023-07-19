<?php
namespace Apie\Core\Persistence\Metadata;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\IdentifierUtils;
use Apie\Core\Persistence\Lists\PersistenceFieldList;
use Apie\Core\Persistence\PersistenceTableInterface;
use ReflectionClass;

final class EntityInvariantMetadata implements PersistenceTableInterface
{
    /**
     * @param class-string<EntityInterface> $class
     */
    public function __construct(
        private readonly BoundedContextId $boundedContextId,
        private readonly string $class,
        private readonly string $invariantPrefix,
        private readonly PersistenceFieldList $fields
    ) {
    }

    public function getName(): string
    {
        return 'apie_entity_' . $this->boundedContextId . '_' . IdentifierUtils::classNameToUnderscore(new ReflectionClass($this->class)) . $this->invariantPrefix;
    }

    public function getFields(): PersistenceFieldList
    {
        return $this->fields;
    }
}
