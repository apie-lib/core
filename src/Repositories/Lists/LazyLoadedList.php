<?php
namespace Apie\Core\Repositories\Lists;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\ItemList;
use Apie\Core\Repositories\Interfaces\CountItems;
use Apie\Core\Repositories\Interfaces\GetItem;
use Apie\Core\Repositories\Interfaces\TakeItem;
use Apie\Core\Repositories\Search\QuerySearch;
use Apie\Core\Repositories\ValueObjects\LazyLoadedListIdentifier;

/**
 * @template T of EntityInterface
 */
final class LazyLoadedList implements EntityInterface
{
    /**
     * @param LazyLoadedListIdentifier<T> $id
     * @param GetItem<T> $getItem
     * @param TakeItem<T> $takeItem
     */
    public function __construct(
        private LazyLoadedListIdentifier $id,
        private GetItem $getItem,
        private TakeItem $takeItem,
        private CountItems $countItems
    ) {
    }

    /**
     * @return LazyLoadedListIdentifier<T>
     */
    public function getId(): LazyLoadedListIdentifier
    {
        return $this->id;
    }

    /**
     * @return PaginatedResult<T>
     */
    public function toPaginatedResult(QuerySearch $search): PaginatedResult
    {
        $index = $search->getPageIndex();
        $count = $search->getItemsPerPage();
        return new PaginatedResult($this->id, $this->totalCount(), new ItemList($this->take($index, $count)), $index, $count, $search);
    }

    /**
     * @return T
     */
    public function get(int $index): EntityInterface
    {
        return ($this->getItem)($index, new QuerySearch(0, 1));
    }

    /**
     * @return T[]
     */
    public function take(int $index, int $count): array
    {
        return ($this->takeItem)($index, $count, new QuerySearch(0));
    }

    public function totalCount(): int
    {
        return ($this->countItems)(new QuerySearch(0));
    }
}
