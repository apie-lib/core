<?php
namespace Apie\Core\SearchFilters;

use Apie\ObjectAccessNormalizer\ObjectAccess\ObjectAccessInterface;
use Apie\ValueObjects\ValueObjectInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class SearchFilterHelper
{
    public static function applyPaginationToSearchFilter(
        array $input,
        SearchFilterRequest  $searchFilterRequest,
        ObjectAccessInterface $objectAccess
    ): Pagerfanta {
        $paginator = new Pagerfanta(new ArrayAdapter(
            array_values(array_filter($input, function ($item) use ($searchFilterRequest, $objectAccess) {
                return self::filter($objectAccess, $item, $searchFilterRequest);
            }))
        ));
        $searchFilterRequest->updatePaginator($paginator);
        return $paginator;
    }

    private static function filter(
        ObjectAccessInterface $accessor,
        $item,
        SearchFilterRequest $searchFilterRequest
    ): bool {
        foreach ($searchFilterRequest->getSearches() as $name => $value) {
            $foundValue = self::getValue($accessor, $item, $name);
            if ($foundValue instanceof ValueObjectInterface) {
                $foundValue = $foundValue->toNative();
            }
            if ($foundValue !== $value) {
                return false;
            }
        }
        return true;
    }

    private static function getValue(ObjectAccessInterface $objectAccess, $resource, string $fieldName)
    {
        if (is_array($resource)) {
            return $resource[$fieldName] ?? null;
        }
        return $objectAccess->getValue($resource, $fieldName);
    }

    /**
     * Applies pagination and search on an array.
     *
     * @param array $input
     * @param SearchFilterRequest $searchFilterRequest
     * @param ObjectAccessInterface $accessor
     * @return array
     */
    public static function applySearchFilter(
        array $input,
        SearchFilterRequest $searchFilterRequest,
        ObjectAccessInterface $accessor
    ) {
        $count = 0;
        $offset = $searchFilterRequest->getOffset();
        $max = $offset + $searchFilterRequest->getNumberOfItems();
        return array_values(array_filter($input, function ($item) use (&$count, $searchFilterRequest, $max, $offset, $accessor) {
            if ($count >= $max) {
                return false;
            }
            if (!self::filter($accessor, $item, $searchFilterRequest)) {
                return false;
            }
            $count++;
            return ($count > $offset);
        }));
    }
}
