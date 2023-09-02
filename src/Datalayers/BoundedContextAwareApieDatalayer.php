<?php
namespace Apie\Core\Datalayers;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Datalayers\Lists\EntityListInterface;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use ReflectionClass;

interface BoundedContextAwareApieDatalayer extends ApieDatalayer
{
    /**
     * @template T of EntityInterface
     * @param ReflectionClass<T> $class
     * @return EntityListInterface<T>
     */
    public function all(ReflectionClass $class, ?BoundedContext $boundedContext = null): EntityListInterface;

    /**
     * @template T of EntityInterface
     * @param IdentifierInterface<T> $identifier
     * @return T
     */
    public function find(IdentifierInterface $identifier, ?BoundedContext $boundedContext = null): EntityInterface;

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistNew(EntityInterface $entity, ?BoundedContext $boundedContext = null): EntityInterface;

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistExisting(EntityInterface $entity, ?BoundedContext $boundedContext = null): EntityInterface;
}
