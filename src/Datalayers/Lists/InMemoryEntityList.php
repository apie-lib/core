<?php
namespace Apie\Core\Datalayers\Lists;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\Concerns\CreatePaginatedResultRuntime;
use Apie\Core\Datalayers\Search\LazyLoadedListFilterer;
use Apie\Core\Entities\EntityInterface;
use ArrayIterator;
use Iterator;
use ReflectionClass;

/**
 * @template T of EntityInterface
 * @implements EntityListInterface<T>
 */
class InMemoryEntityList implements EntityListInterface
{
    use CreatePaginatedResultRuntime;

    /**
     * @param ReflectionClass<T> $class
     * @param array<int, T> $entityList
     */
    public function __construct(
        private ReflectionClass $class,
        private BoundedContextId $boundedContextId,
        private LazyLoadedListFilterer $filterer,
        private array& $entityList
    ) {
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->entityList);
    }

    public function getTotalCount(): int
    {
        return count($this->entityList);
    }
}
