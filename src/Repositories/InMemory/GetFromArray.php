<?php
namespace Apie\Core\Repositories\InMemory;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Repositories\Interfaces\GetItem;
use Apie\Core\Repositories\Search\QuerySearch;

/**
 * @template T of EntityInterface
 * @implements GetItem<T>
 */
class GetFromArray implements GetItem
{
    /**
     * @param array<int, T> $array
     */
    public function __construct(private array& $array)
    {
    }

    public function __invoke(int $index, QuerySearch $search): EntityInterface
    {
        return $this->array[$index];
    }
}
