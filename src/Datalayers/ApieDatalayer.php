<?php
namespace Apie\Core\Datalayers;

use Apie\Core\Datalayers\Lists\LazyLoadedList;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use ReflectionClass;

interface ApieDatalayer
{
    /**
     * @template T of EntityInterface
     * @param ReflectionClass<T> $class
     * @return LazyLoadedList<T>
     */
    public function all(ReflectionClass $class): LazyLoadedList;

    /**
     * @template T of EntityInterface
     * @param IdentifierInterface<T> $identifier
     * @return T
     */
    public function find(IdentifierInterface $identifier): EntityInterface;

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistNew(EntityInterface $entity): EntityInterface;

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistExisting(EntityInterface $entity): EntityInterface;
}
