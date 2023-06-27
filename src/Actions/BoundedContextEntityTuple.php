<?php
namespace Apie\Core\Actions;

use Apie\Core\BoundedContext\BoundedContext;
use ReflectionClass;

final class BoundedContextEntityTuple
{
    public function __construct(
        public readonly BoundedContext $boundedContext,
        public readonly ReflectionClass $resourceClass
    ) {
    }
}
