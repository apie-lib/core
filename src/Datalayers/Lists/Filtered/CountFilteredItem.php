<?php
namespace Apie\Core\Datalayers\Lists\Filtered;

use Apie\Core\Datalayers\Interfaces\CountItems;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Entities\EntityInterface;

final class CountFilteredItem implements CountItems
{
    /**
     * @param FilteredItem<EntityInterface> $filteredItem
     */
    public function __construct(private readonly FilteredItem $filteredItem)
    {
    }

    public function __invoke(QuerySearch $search): int
    {
        $this->filteredItem->hydrateAll();
        return $this->filteredItem->countHydratedItems();
    }
}
