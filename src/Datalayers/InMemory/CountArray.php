<?php
namespace Apie\Core\Datalayers\InMemory;

use Apie\Core\Datalayers\Interfaces\CountItems;
use Apie\Core\Datalayers\Search\QuerySearch;

class CountArray implements CountItems
{
    /**
     * @param array<int, mixed> $array
     */
    public function __construct(private array& $array)
    {
    }

    public function __invoke(QuerySearch $search): int
    {
        return count($this->array);
    }
}
