<?php
namespace Apie\Core\Indexing;

use Apie\Core\Attributes\ProvideIndex;
use Apie\Core\Context\ApieContext;
use ReflectionAttribute;
use ReflectionClass;

class FromAttribute implements IndexingStrategyInterface
{
    public function support(object $object): bool
    {
        $refl = new ReflectionClass($object);
        return ! empty($refl->getAttributes(ProvideIndex::class));
    }

    /**
     * @return array<string, int>
     */
    public function getIndexes(object $object, ApieContext $context, Indexer $indexer): array
    {
        $refl = new ReflectionClass($object);
        $result = [];
        $attributes = $refl->getAttributes(ProvideIndex::class);
        foreach ($attributes as $attribute) {
            /** @var ReflectionAttribute<ProvideIndex> $attribute */
            $method = $refl->getMethod($attribute->newInstance()->methodName);
            $result += $method->invoke($object);
        }
        return $result;
    }
}
