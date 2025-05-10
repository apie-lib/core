<?php
namespace Apie\Core\Datalayers;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\Events\EntityPersisted;
use Apie\Core\Datalayers\Grouped\DataLayerByBoundedContext;
use Apie\Core\Datalayers\Lists\EntityListInterface;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Core\Lists\StringSet;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionClass;

final class GroupedDataLayer implements ApieDatalayerWithFilters, ApieDatalayer, ApieDatalayerWithSupport
{
    public function __construct(
        private readonly DataLayerByBoundedContext $hashmap,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function isSupported(EntityInterface|ReflectionClass|IdentifierInterface $instance, BoundedContextId $boundedContextId): bool
    {
        if ($instance instanceof EntityInterface) {
            $instance = $instance->getId()->getReferenceFor();
        } elseif ($instance instanceof IdentifierInterface) {
            $instance = $instance->getReferenceFor();
        }
        $datalayer = $this->hashmap->pickDataLayerFor($instance, $boundedContextId);
        if ($datalayer instanceof ApieDatalayerWithSupport) {
            return $datalayer->isSupported($instance, $boundedContextId);
        }
        return true;
    }

    public function getFilterColumns(ReflectionClass $class, BoundedContextId $boundedContextId): ?StringSet
    {
        $datalayer = $this->hashmap->pickDataLayerFor($class, $boundedContextId);
        if ($datalayer instanceof ApieDatalayerWithFilters) {
            return $datalayer->getFilterColumns($class, $boundedContextId);
        }
        return null;
    }

    public function getOrderByColumns(ReflectionClass $class, BoundedContextId $boundedContextId): ?StringSet
    {
        $datalayer = $this->hashmap->pickDataLayerFor($class, $boundedContextId);
        if ($datalayer instanceof ApieDatalayerWithFilters) {
            return $datalayer->getOrderByColumns($class, $boundedContextId);
        }
        return null;
    }

    public function all(ReflectionClass $class, ?BoundedContextId $boundedContextId = null): EntityListInterface
    {
        return $this->hashmap->pickDataLayerFor($class, $boundedContextId)
            ->all($class, $boundedContextId);
    }

    public function find(IdentifierInterface $identifier, ?BoundedContextId $boundedContextId = null): EntityInterface
    {
        return $this->hashmap->pickDataLayerFor($identifier::getReferenceFor(), $boundedContextId)
            ->find($identifier, $boundedContextId);
    }

    private function dispatch(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): EntityInterface
    {
        $this->dispatcher->dispatch(
            new EntityPersisted(
                $entity,
                $boundedContextId
            )
        );
        return $entity;
    }

    public function persistNew(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): EntityInterface
    {
        return $this->dispatch(
            $this->hashmap->pickDataLayerFor($entity->getId()::getReferenceFor(), $boundedContextId)
                ->persistNew($entity, $boundedContextId),
            $boundedContextId
        );
    }

    public function persistExisting(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): EntityInterface
    {
        return $this->dispatch(
            $this->hashmap->pickDataLayerFor($entity->getId()::getReferenceFor(), $boundedContextId)
                ->persistExisting($entity, $boundedContextId),
            $boundedContextId
        );
    }
    
    public function removeExisting(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): void
    {
        $this->hashmap->pickDataLayerFor($entity->getId()::getReferenceFor(), $boundedContextId)
            ->removeExisting($entity, $boundedContextId);
    }

    public function upsert(EntityInterface $entity, ?BoundedContextId $boundedContextId): EntityInterface
    {
        return $this->dispatch(
            $this->hashmap->pickDataLayerFor($entity->getId()::getReferenceFor(), $boundedContextId)
                ->upsert($entity, $boundedContextId),
            $boundedContextId
        );
    }
}
