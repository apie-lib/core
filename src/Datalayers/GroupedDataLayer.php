<?php
namespace Apie\Core\Datalayers;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\Grouped\DataLayerByBoundedContext;
use Apie\Core\Datalayers\Lists\EntityListInterface;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use ReflectionClass;

final class GroupedDataLayer implements BoundedContextAwareApieDatalayer, ApieDatalayerWithSupport
{
    public function __construct(private readonly DataLayerByBoundedContext $hashmap)
    {
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

    public function all(ReflectionClass $class, ?BoundedContext $boundedContext = null): EntityListInterface
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
