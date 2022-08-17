<?php
namespace Apie\Core\Repositories\Lists;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\ItemList;
use Apie\Core\Repositories\Search\QuerySearch;
use Apie\Core\Repositories\ValueObjects\LazyLoadedListIdentifier;

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
