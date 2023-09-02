<?php
namespace Apie\Core\Datalayers\Search;

use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Indexing\Indexer;
use Apie\Core\PropertyAccess;
use Apie\Core\ValueObjects\Utils;

final class LazyLoadedListFilterer
{
    public function __construct(
        private readonly Indexer $indexer
    ) {
    }

    public function appliesSearch(EntityInterface $object, QuerySearch $querySearch, ApieContext $apieContext = new ApieContext()): bool
    {
        $searchTerm = $querySearch->getTextSearch();
        $indexes = $this->indexer->getIndexesForObject($object, $apieContext);
        if (in_array($searchTerm, $indexes) || in_array(strtoupper($searchTerm), $indexes) || in_array(strtolower($searchTerm), $indexes)) {
            return true;
        }
        foreach ($querySearch->getSearches() as $searchTerm => $searchValue) {
            if ($this->compare($searchValue, PropertyAccess::getPropertyValue($object, explode('.', $searchTerm), $apieContext, false))) {
                return true;
            }
        }

        return false;
    }

    public function appliesPartialSearch(EntityInterface $object, QuerySearch $querySearch, ApieContext $apieContext = new ApieContext()): bool
    {
        $searchTerm = $querySearch->getTextSearch();
        if (null === $searchTerm) {
            return true;
        }
        $indexes = $this->indexer->getIndexesForObject($object, $apieContext);
        foreach (array_keys($indexes) as $index) {
            if (strpos($index, $searchTerm) !== false) {
                return true;
            }
        }
        
        return false;
    }

    private function compare(string $value1, mixed $value2): bool
    {
        $value2 = Utils::toString($value2);
        return $value1 === $value2;
    }
}
