<?php
namespace Apie\Core\Datalayers\Concerns;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\Datalayers\ApieDatalayerWithFilters;
use Apie\Core\Lists\StringList;
use Apie\Core\Metadata\MetadataFactory;
use ReflectionClass;

/**
 * @see ApieDatalayerWithFilters
 */
trait FiltersOnAllFields
{
    public function getFilterColumns(
        ReflectionClass $class,
        BoundedContextId $boundedContextId
    ): StringList {
        $metadata = MetadataFactory::getResultMetadata($class, new ApieContext());
        $list = [];
        $hashmap = $metadata->getHashmap()->toArray();
        foreach ($class->getProperties() as $property) {
            if (isset($hashmap[$property->name])) {
                $list[] = $property->name;
            }
        }

        return new StringList($list);
    }

    public function getOrderByColumns(
        ReflectionClass $class,
        BoundedContextId $boundedContextId
    ): ?StringList {
        return null;
    }
}
