<?php
namespace Apie\Core\Interfaces;

use Apie\Core\Models\ApiResourceClassMetadata;
use Apie\Core\SearchFilters\SearchFilter;

/**
 * If a retriever implements this interface as well, it can add search filter arguments.
 */
interface SearchFilterProviderInterface
{
    /**
     * Retrieves search filter for an api resource.
     */
    public function getSearchFilter(ApiResourceClassMetadata $classMetadata): SearchFilter;
}
