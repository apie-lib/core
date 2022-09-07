<?php
namespace Apie\Core\BoundedContext;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\ReflectionClassList;
use Apie\Core\Lists\ReflectionMethodList;

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

    public function getId(): BoundedContextId
    {
        return $this->id;
    }
}
