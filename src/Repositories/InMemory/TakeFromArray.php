<?php
namespace Apie\Core\Repositories\InMemory;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Repositories\Interfaces\TakeItem;
use Apie\Core\Repositories\Search\QuerySearch;

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
