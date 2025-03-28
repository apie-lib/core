<?php
namespace Apie\Core\Datalayers\Concerns;

use Apie\Core\Attributes\SearchFilterOption;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\Datalayers\ApieDatalayerWithFilters;
use Apie\Core\Lists\StringSet;
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
    ): StringSet {
        $metadata = MetadataFactory::getResultMetadata($class, new ApieContext());
        $list = [];
        $hashmap = $metadata->getHashmap()->toArray();
        foreach ($class->getProperties() as $property) {
            $enabled = true;
            foreach ($property->getAttributes(SearchFilterOption::class) as $attr) {
                $enabled = $enabled & $attr->newInstance()->enabled;
            }
            if (isset($hashmap[$property->name]) && $enabled) {
                $list[] = $property->name;
            }
        }

        return new StringSet($list);
    }

    public function getOrderByColumns(
        ReflectionClass $class,
        BoundedContextId $boundedContextId
    ): ?StringSet {
        return null;
    }
}
