<?php
namespace Apie\Core\Datalayers\InMemory;

use Apie\Core\Datalayers\Interfaces\CountItems;
use Apie\Core\Datalayers\Search\QuerySearch;

class CountArray implements CountItems
{
    /**
     * @var callable(): array<int, mixed> $getArray
     */
    private $getArray;
    
    /**
     * @param callable(): array<int, mixed> $getArray
     */
    public function __construct(callable $getArray)
    {
        $this->getArray = $getArray;
    }

    public function __invoke(QuerySearch $search): int
    {
        return count(($this->getArray)());
    }
}
