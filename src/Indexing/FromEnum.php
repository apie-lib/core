<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
use BackedEnum;
use ReflectionClass;
use UnitEnum;

class FromEnum implements IndexingStrategyInterface
{
    public function support(object $object): bool
    {
        $refl = new ReflectionClass($object);
        return $refl->isEnum();
    }

    /**
     * @return array<string, int>
     */
    public function getIndexes(object $object, ApieContext $context, Indexer $indexer): array
    {
        assert($object instanceof UnitEnum);
        if ($object instanceof BackedEnum) {
            return [$object->name => 1, $object->value => 1];
        }
        return [$object->name => 1];
    }
}
