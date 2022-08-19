<?php
namespace Apie\Core\Datalayers\InMemory;

use Apie\Core\Datalayers\Interfaces\TakeItem;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Entities\EntityInterface;

/**
 * @template T of EntityInterface
 * @implements TakeItem<T>
 */
class TakeFromArray implements TakeItem
{
    /**
     * @param array<int, T> $array
     */
    public function __construct(private array& $array)
    {
    }

    public function __invoke(int $index, int $count, QuerySearch $search): array
    {
        return array_slice($this->array, $index, $count);
    }
}
