<?php
namespace Apie\Core\Persistence;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Entities\EntityInterface;
use ReflectionClass;

interface PersistenceMetadataFactoryInterface
{
    /**
     * @param ReflectionClass<EntityInterface> $entity
     */
    public function createEntityMetadata(
        ReflectionClass $entity,
        BoundedContext $boundedContext,
        ?PersistenceMetadataContext $context = null
    ): PersistenceTableInterface;

    public function createRelatedTable(PersistenceMetadataContext $context): PersistenceTableInterface;

    public function createProperty(PersistenceMetadataContext $context): ?PersistenceFieldInterface;
}
