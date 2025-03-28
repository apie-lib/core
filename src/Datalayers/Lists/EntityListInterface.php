<?php
namespace Apie\Core\Datalayers\Lists;

use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Entities\EntityInterface;
use IteratorAggregate;

/**
 * @template T of EntityInterface
 * @extends IteratorAggregate<int, T>
 */
interface EntityListInterface extends IteratorAggregate
{
    /**
     * @return PaginatedResult<T>
     */
    public function toPaginatedResult(QuerySearch $search): PaginatedResult;

    public function getTotalCount(): int;
}
