<?php
namespace Apie\Core\Datalayers\Search;

use Apie\Core\ContextConstants;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Indexing\Indexer;
use Apie\Core\Lists\PermissionList;
use Apie\Core\Permissions\PermissionInterface;
use Apie\Core\Permissions\RequiresPermissionsInterface;
use Apie\Core\PropertyAccess;
use Apie\Core\ValueObjects\Utils;
use Exception;

final class LazyLoadedListFilterer
{
    public function __construct(
        private readonly Indexer $indexer
    ) {
    }

    public function appliesFiltering(EntityInterface $object, QuerySearch $querySearch): bool
    {
        return $this->appliesSearch($object, $querySearch)
            && $this->appliesPartialSearch($object, $querySearch)
            && $this->appliesPermissions($object, $querySearch);
    }

    public function appliesPermissions(EntityInterface $object, QuerySearch $querySearch): bool
    {
        if (!$querySearch->getApieContext()->withContext(ContextConstants::RESOURCE, $object)->isAuthorized(true)) {
            return false;
        }
        if ($object instanceof RequiresPermissionsInterface) {
            $requiredPermissions = $object->getRequiredPermissions();
            $user = $querySearch->getApieContext()->getContext(ContextConstants::AUTHENTICATED_USER, false);
            if ($user instanceof PermissionInterface) {
                $hasPermisions = $user->getPermissionIdentifiers();
                return $hasPermisions->hasOverlap($requiredPermissions);
            }
            return (new PermissionList(['']))->hasOverlap($requiredPermissions);
        }
        return true;
    }

    public function appliesSearch(EntityInterface $object, QuerySearch $querySearch): bool
    {
        $apieContext = $querySearch->getApieContext();
        $searchTerm = $querySearch->getTextSearch();
        $indexes = $this->indexer->getIndexesForObject($object, $apieContext);
        if ($searchTerm && (in_array($searchTerm, $indexes) || in_array(strtoupper($searchTerm), $indexes) || in_array(strtolower($searchTerm), $indexes))) {
            return true;
        }
        $searches = $querySearch->getSearches()->toArray();
        if (empty($searches)) {
            return true;
        }
        foreach ($searches as $searchTerm => $searchValue) {
            if ($this->compare($searchValue, PropertyAccess::getPropertyValue($object, explode('.', $searchTerm), $apieContext, false))) {
                return true;
            }
        }

        return false;
    }

    public function appliesPartialSearch(EntityInterface $object, QuerySearch $querySearch): bool
    {
        $apieContext = $querySearch->getApieContext();
        $searchTerm = $querySearch->getTextSearch();
        if (null === $searchTerm) {
            return true;
        }
        $searchTerm = trim($searchTerm);
        $indexes = $this->indexer->getIndexesForObject($object, $apieContext);
        foreach (array_keys($indexes) as $index) {
            if (stripos($index, $searchTerm) !== false) {
                return true;
            }
        }
        
        return false;
    }

    private function compare(string $value1, mixed $value2): bool
    {
        try {
            $value2 = Utils::toString($value2);
        } catch (Exception) {
            return false;
        }
        return strpos($value2, $value1) !== false;
    }
}
