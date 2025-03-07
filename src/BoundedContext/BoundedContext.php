<?php
namespace Apie\Core\BoundedContext;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\ReflectionClassList;
use Apie\Core\Lists\ReflectionMethodList;
use ReflectionClass;

final class BoundedContext implements EntityInterface
{
    public readonly BoundedContextId $id;

    public function __construct(
        BoundedContextId|string $id,
        public readonly ReflectionClassList $resources,
        public readonly ReflectionMethodList $actions,
    ) {
        $this->id = $id instanceof BoundedContextId ? $id : new BoundedContextId($id);
    }

    /**
     * @param ReflectionClass<object> $resourceClass
     */
    public function contains(ReflectionClass $resourceClass): bool
    {
        foreach ($this->resources as $resource) {
            if ($resource->name === $resourceClass->name) {
                return true;
            }
        }

        return false;
    }

    public function getId(): BoundedContextId
    {
        return $this->id;
    }
}
