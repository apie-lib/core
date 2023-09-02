<?php
namespace Apie\Core\Datalayers\Concerns;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\Datalayers\Lists\PaginatedResult;
use Apie\Core\Datalayers\Search\LazyLoadedListFilterer;
use Apie\Core\Datalayers\Search\QuerySearch;
use Apie\Core\Datalayers\ValueObjects\LazyLoadedListIdentifier;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\ItemList;
use Iterator;
use ReflectionClass;

/**
 * @property LazyLoadedListFilterer $filterer
 * @property BoundedContextId $boundedContextId
 * @property ReflectionClass<EntityInterface> $class
 */
trait CreatePaginatedResultRuntime
{
    abstract public function getIterator(): Iterator;

    public function toPaginatedResult(QuerySearch $search, ApieContext $apieContext = new ApieContext()): PaginatedResult
    {
        $found = 0;
        $pageIndex = $search->getPageIndex();
        $itemsPerPage = $search->getItemsPerPage();
        $offset = 1+ $pageIndex * $itemsPerPage;
        $endOffset = $offset + $itemsPerPage;
        $filteredList = [];
        $totalCount = 0;
        foreach ($this->getIterator() as $entity) {
            assert($entity instanceof EntityInterface);
            $added = $found < $endOffset && $this->filterer->appliesPartialSearch($entity, $search, $apieContext);
            $totalCount++;
            if ($added) {
                $found++;
                if ($found >= $offset && $found < $endOffset) {
                    $filteredList[] = $entity;
                }
            }
        }
        return new PaginatedResult(
            LazyLoadedListIdentifier::createFrom($this->boundedContextId, $this->class),
            $totalCount,
            new ItemList($filteredList),
            $pageIndex,
            $itemsPerPage,
            $search
        );
    }
}
