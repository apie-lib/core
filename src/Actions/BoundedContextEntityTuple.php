<?php
namespace Apie\Core\Actions;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Entities\EntityInterface;
use ReflectionClass;

final class BoundedContextEntityTuple
{
    /**
     * @param ReflectionClass<EntityInterface> $resourceClass
     */
    public function __construct(
        public readonly BoundedContext $boundedContext,
        public readonly ReflectionClass $resourceClass
    ) {
    }
}
