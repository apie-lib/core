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
     * @var callable(): array<int, T> $getArray
     */
    private $getArray;
    
    /**
     * @param callable(): array<int, T> $getArray
     */
    public function __construct(callable $getArray)
    {
        $this->getArray = $getArray;
    }

    public function __invoke(int $index, int $count, QuerySearch $search): array
    {
        return array_slice(($this->getArray)(), $index, $count);
    }
}
