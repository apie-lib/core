<?php
namespace Apie\Core\Datalayers;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\StringSet;
use ReflectionClass;

/**
 * Implement this interface to a data layer class to add filters for the get resource list endpoint.
 */
interface ApieDatalayerWithFilters extends ApieDatalayer
{
    /**
     * @param ReflectionClass<covariant EntityInterface> $class
     */
    public function getFilterColumns(ReflectionClass $class, BoundedContextId $boundedContextId): ?StringSet;

    /**
     * @param ReflectionClass<covariant EntityInterface> $class
     */
    public function getOrderByColumns(ReflectionClass $class, BoundedContextId $boundedContextId): ?StringSet;
}
