<?php
namespace Apie\Core\BoundedContext;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\ReflectionClassList;
use Apie\Core\Lists\ReflectionMethodList;

final class BoundedContext implements EntityInterface
{
    public function __construct(
        public readonly BoundedContextId $id,
        public readonly ReflectionClassList $resources,
        public readonly ReflectionMethodList $actions,
    ) {
    }

    public function getId(): BoundedContextId
    {
        return $this->id;
    }
}
