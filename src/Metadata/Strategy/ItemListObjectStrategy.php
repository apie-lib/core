<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemList;
use Apie\Core\Metadata\ItemListMetadata;
use Apie\Core\Metadata\StrategyInterface;
use ReflectionClass;

final class ItemListObjectStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return $class->name === ItemList::class || $class->isSubclassOf(ItemList::class);
    }

    /**
     * @param ReflectionClass<ItemList> $class
     */
    public function __construct(private readonly ReflectionClass $class)
    {
    }

    public function getCreationMetadata(ApieContext $context): ItemListMetadata
    {
        return new ItemListMetadata($this->class, true);
    }

    public function getModificationMetadata(ApieContext $context): ItemListMetadata
    {
        return new ItemListMetadata($this->class, true);
    }

    public function getResultMetadata(ApieContext $context): ItemListMetadata
    {
        return new ItemListMetadata($this->class, false);
    }
}
