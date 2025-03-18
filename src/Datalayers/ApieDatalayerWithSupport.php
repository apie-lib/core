<?php
namespace Apie\Core\Datalayers;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use ReflectionClass;

interface ApieDatalayerWithSupport extends ApieDatalayer
{
    /**
     * @param EntityInterface|IdentifierInterface<EntityInterface>|ReflectionClass<EntityInterface|IdentifierInterface<EntityInterface>> $instance
     */
    public function isSupported(
        EntityInterface|ReflectionClass|IdentifierInterface $instance,
        BoundedContextId $boundedContextId
    ): bool;
}
