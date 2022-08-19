<?php
namespace Apie\Core\Datalayers\Lists;

use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Datalayers\ValueObjects\LazyLoadedListIdentifier;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\ItemList;

/**
 * @template T of EntityInterface
 */
final class PaginatedResult
{
    /**
     * @param LazyLoadedListIdentifier<T> $id
     * @param ItemList<T> $list
     */
    public function __construct(
        public LazyLoadedListIdentifier $id,
        public readonly int $totalCount,
        public readonly ItemList $list,
        public readonly int $pageNumber,
        public readonly int $pageSize,
        public readonly QuerySearch $querySearch
    ) {
    }
}
