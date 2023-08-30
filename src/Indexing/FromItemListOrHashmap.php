<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
use ReflectionClass;

class FromItemListOrHashmap implements IndexingStrategyInterface
{
    public function support(object $object): bool
    {
        return (new ReflectionClass(ItemList::class))->isInstance($object)
            ||  (new ReflectionClass(ItemHashmap::class))->isInstance($object);
    }

    /**
     * @return array<string, int>
     */
    public function getIndexes(object $object, ApieContext $context, Indexer $indexer): array
    {
        $result = [];
        if ($object instanceof ItemList) {
            foreach ($object as $item) {
                $objectResult = $indexer->getIndexesForObject($item, $context, $indexer);
                $result = Indexer::merge($result, $objectResult);
            }
            return $result;
        }
        assert($object instanceof ItemHashmap);
        foreach ($object as $key => $item) {
            $result[$key] = ($result[$key] ?? 0) + 1;
            $objectResult = $indexer->getIndexesForObject($item, $context, $indexer);
            $result = Indexer::merge($result, $objectResult);
        }
        return $result;
    }
}
