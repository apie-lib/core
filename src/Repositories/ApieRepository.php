<?php
namespace Apie\Core\Repositories;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Core\Repositories\Lists\LazyLoadedList;
use ReflectionClass;

interface ApieRepository
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
}
