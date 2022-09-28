<?php
namespace Apie\Core\Datalayers;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Datalayers\Grouped\DataLayerByBoundedContext;
use Apie\Core\Datalayers\Lists\LazyLoadedList;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use ReflectionClass;

final class GroupedDataLayer implements BoundedContextAwareApieDatalayer
{
    public function __construct(private readonly DataLayerByBoundedContext $hashmap)
    {
    }

    public function all(ReflectionClass $class, ?BoundedContext $boundedContext = null): LazyLoadedList
    {
        return $this->hashmap->pickDataLayerFor($class, $boundedContext->getId())
            ->all($class, $boundedContext);
    }

    public function find(IdentifierInterface $identifier, ?BoundedContext $boundedContext = null): EntityInterface
    {
        return $this->hashmap->pickDataLayerFor($identifier::getReferenceFor(), $boundedContext->getId())
            ->find($identifier, $boundedContext);
    }

    public function persistNew(EntityInterface $entity, ?BoundedContext $boundedContext = null): EntityInterface
    {
        return $this->hashmap->pickDataLayerFor($entity->getId()::getReferenceFor(), $boundedContext->getId())
            ->persistNew($entity, $boundedContext);
    }

    public function persistExisting(EntityInterface $entity, ?BoundedContext $boundedContext = null): EntityInterface
    {
        return $this->hashmap->pickDataLayerFor($entity->getId()::getReferenceFor(), $boundedContext->getId())
            ->persistExisting($entity, $boundedContext);
    }
}
