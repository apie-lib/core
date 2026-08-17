<?php
namespace Apie\Core\BoundedContext;

use Apie\Core\Actions\BoundedContextHashmapIterator;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Core\Lists\ItemHashmap;
use ReflectionClass;

/**
 * Contains multiple bounded contexts mapped by key.
 */
final class BoundedContextHashmap extends ItemHashmap
{
    protected bool $mutable = false;

    public function offsetGet(mixed $offset): BoundedContext
    {
        return parent::offsetGet($offset);
    }

    public function getTupleIterator(): BoundedContextHashmapIterator
    {
        return new BoundedContextHashmapIterator($this);
    }

    /**
     * @param ReflectionClass<EntityInterface>|ReflectionClass<IdentifierInterface<EntityInterface>> $class
     */
    public function getBoundedContextFromClassName(ReflectionClass $class, ?BoundedContextId $prio = null): ?BoundedContext
    {
        if (in_array(IdentifierInterface::class, $class->getInterfaceNames())) {
            $class = $class->getMethod('getReferenceFor')->invoke(null);
        }
        if ($prio && isset($this[$prio->toNative()])) {
            $boundedContext = $this[$prio->toNative()];
            if ($boundedContext->contains($class)) {
                return $boundedContext;
            }
        }
        foreach ($this as $boundedContext) {
            if ($boundedContext->contains($class)) {
                return $boundedContext;
            }
        }
        return null;
    }
}
