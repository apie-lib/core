<?php
namespace Apie\Core\Datalayers;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Lists\StringList;
use ReflectionClass;

/**
 * Implement this interface to a data layer class to add filters for the get resource list endpoint.
 */
interface ApieDatalayerWithFilters extends ApieDatalayer
{
    /**
     * @param ReflectionClass<object> $class
     */
    public function getFilterColumns(ReflectionClass $class, BoundedContextId $boundedContextId): ?StringList;

    /**
     * @param ReflectionClass<object> $class
     */
    public function getOrderByColumns(ReflectionClass $class, BoundedContextId $boundedContextId): ?StringList;
}
