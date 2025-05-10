<?php
namespace Apie\Core\Datalayers\Events;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Entities\EntityInterface;

final class EntityPersisted
{
    public function __construct(
        public readonly EntityInterface $entity,
        public readonly ?BoundedContextId $boundedContextId
    ) {
    }
}
