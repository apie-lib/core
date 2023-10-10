<?php
namespace Apie\Core\Datalayers;

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
    public function all(ReflectionClass $class): EntityListInterface;

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

    public function removeExisting(EntityInterface $entity): void;
}
