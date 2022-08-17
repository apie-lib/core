<?php
namespace Apie\Core\Repositories\InMemory;

use Apie\Core\Repositories\Interfaces\CountItems;
use Apie\Core\Repositories\Search\QuerySearch;

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
