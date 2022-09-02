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

    public function __invoke(int $index, QuerySearch $search): EntityInterface
    {
        return ($this->getArray)()[$index];
    }
}
