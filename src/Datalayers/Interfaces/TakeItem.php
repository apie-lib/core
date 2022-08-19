<?php
namespace Apie\Core\Datalayers\Interfaces;

use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Entities\EntityInterface;

/**
 * @template T of EntityInterface
 */
interface TakeItem
{
    /**
     * @return T[]
     */
    public function __invoke(int $index, int $count, QuerySearch $search): array;
}
