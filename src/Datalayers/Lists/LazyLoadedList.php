<?php
namespace Apie\Core\Datalayers\Lists;

use Apie\Core\Datalayers\InMemory\CountArray;
use Apie\Core\Datalayers\InMemory\GetFromArray;
use Apie\Core\Datalayers\InMemory\TakeFromArray;
use Apie\Core\Datalayers\Interfaces\CountItems;
use Apie\Core\Datalayers\Interfaces\GetItem;
use Apie\Core\Datalayers\Interfaces\TakeItem;
use Apie\Core\Datalayers\Lists\Filtered\CountFilteredItem;
use Apie\Core\Datalayers\Lists\Filtered\FilteredItem;
use Apie\Core\Datalayers\Lists\Filtered\TakeFilteredItem;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Datalayers\ValueObjects\LazyLoadedListIdentifier;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\ItemList;
use Iterator;
use IteratorAggregate;

/**
 * @template T of EntityInterface
 * @implements IteratorAggregate<int, EntityInterface>
 */
final class LazyLoadedList implements EntityInterface, IteratorAggregate
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

    public function getIterator(): Iterator
    {
        $total = $this->totalCount();
        return new ItemList($this->take(0, $total));
    }

    /**
     * @param LazyLoadedListIdentifier<T> $id
     * @param array<int, T> $input
     * @return LazyLoadedList<T>
     */
    public static function createFromArray(LazyLoadedListIdentifier $id, array $input): self
    {
        $callable = function () use ($input) {
            return $input;
        };
        return new LazyLoadedList(
            $id,
            new GetFromArray($callable),
            new TakeFromArray($callable),
            new CountArray($callable)
        );
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
        return new PaginatedResult($this->id, $this->totalCount(), new ItemList($this->take($index * $count, $count)), $index, $count, $search);
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

    /**
     * @param callable(T): bool $filterFn
     * @return LazyLoadedList<T>
     */
    public function filterList(callable $filterFn): self
    {
        $filteredList = new FilteredItem(
            $this->getItem,
            $this->totalCount(),
            $filterFn
        );

        return new self(
            $this->id,
            $filteredList,
            new TakeFilteredItem($filteredList),
            new CountFilteredItem($filteredList)
        );
    }
}
