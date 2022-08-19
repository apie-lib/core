<?php
namespace Apie\Core\Datalayers\InMemory;

use Apie\Core\Datalayers\Interfaces\GetItem;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Entities\EntityInterface;

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
