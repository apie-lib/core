<?php
namespace Apie\Core\Datalayers;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\Lists\EntityListInterface;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use ReflectionClass;

interface ApieDatalayer
{
    /**
     * @template T of EntityInterface
     * @param ReflectionClass<T> $class
     * @return EntityListInterface<T>
     */
    public function all(ReflectionClass $class, ?BoundedContextId $boundedContextId = null): EntityListInterface;

    /**
     * @template T of EntityInterface
     * @param IdentifierInterface<T> $identifier
     * @return T
     */
    public function find(IdentifierInterface $identifier, ?BoundedContextId $boundedContextId = null): EntityInterface;

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistNew(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): EntityInterface;

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistExisting(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): EntityInterface;

    public function removeExisting(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): void;

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function upsert(EntityInterface $entity, ?BoundedContextId $boundedContextId): EntityInterface;
}
