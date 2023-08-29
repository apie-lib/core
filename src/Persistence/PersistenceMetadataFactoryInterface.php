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

    /**
     * @param ReflectionClass<object> $class
     */
    public function createInvariantTable(
        ReflectionClass $class,
        PersistenceMetadataContext $context
    ): PersistenceTableInterface;

    /**
     * @param ReflectionClass<EntityInterface> $class
     */
    public function createIndexTable(
        ReflectionClass $class,
        BoundedContext $boundedContext
    ): PersistenceTableInterface;

    public function createProperty(PersistenceMetadataContext $context): ?PersistenceFieldInterface;
}
