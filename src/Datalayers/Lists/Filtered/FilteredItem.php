<?php
namespace Apie\Core\Datalayers\Lists\Filtered;

use Apie\Core\Datalayers\Interfaces\CountItems;
use Apie\Core\Datalayers\Interfaces\GetItem;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Entities\EntityInterface;

/**
 * @template T of EntityInterface
 * @implements GetItem<T>
 */
final class FilteredItem implements GetItem
{
    private array $hydrated = [];
    private int $currentIndex = 0;
    /**
     * @var callable(T): bool $filterFn
     */
    private readonly array|string|object $filterFn;

    /**
     * @param GetItem<T> $getItem
     * @param CountItems<T> $countItem
     * @param callable(T): bool $filterFn
     */
    public function __construct(
        private readonly GetItem $getItem,
        private readonly int $maxItems,
        callable $filterFn
    ) {
        $this->filterFn = $filterFn;
    }

    public function hydrateAll(): void
    {
        $this->hydrateTillIndex($this->maxItems);
    }

    public function countHydratedItems(): int
    {
        return count($this->hydrated);
    }

    private function hydrateTillIndex(int $index): void
    {
        if (count($this->hydrated) > $index) {
            return;
        }
        $search = new QuerySearch(0);
        while ($this->currentIndex < $this->maxItems || count($this->hydrated) > $index) {
            $item = ($this->getItem)($this->currentIndex, $search);
            if (($this->filterFn)($item)) {
                $this->hydrated[] = $item;
            }
            $this->currentIndex++;
        }
    }

    public function __invoke(int $index, QuerySearch $search): EntityInterface
    {
        $this->hydrateTillIndex($index);
        return $this->hydrated[$index];
    }
}
