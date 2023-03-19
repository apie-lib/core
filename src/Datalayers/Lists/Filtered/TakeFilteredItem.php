<?php
namespace Apie\Core\Datalayers\Lists\Filtered;

use Apie\Core\Datalayers\Interfaces\TakeItem;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Entities\EntityInterface;

/**
 * @template T of EntityInterface
 * @implements TakeItem<T>
 */
final class TakeFilteredItem implements TakeItem
{
    /**
     * @param FilteredItem<T> $filteredItem
     */
    public function __construct(private readonly FilteredItem $filteredItem)
    {
    }

    public function __invoke(int $index, int $count, QuerySearch $search): array
    {
        $res = [];
        for ($i = 0; $i < $count; $i++) {
            $res[] = $this->filteredItem->__invoke($index + $i, $search);
        }

        return $res;
    }
}
