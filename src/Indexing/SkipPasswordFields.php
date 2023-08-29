<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\IsPasswordValueObject;
use ReflectionClass;

class SkipPasswordFields implements IndexingStrategyInterface
{
    public function support(object $object): bool
    {
        $refl = new ReflectionClass($object);
        return $object instanceof ValueObjectInterface && in_array(IsPasswordValueObject::class, $refl->getTraitNames());
    }

    public function getIndexes(object $object, ApieContext $context, Indexer $indexer): array
    {
        return [];
    }
}
