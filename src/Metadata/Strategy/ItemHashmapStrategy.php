<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Metadata\ItemHashmapMetadata;
use Apie\Core\Metadata\StrategyInterface;
use ReflectionClass;

final class ItemHashmapStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return $class->name === ItemHashmap::class || $class->isSubclassOf(ItemHashmap::class);
    }

    /**
     * @param ReflectionClass<ItemHashmap> $class
     */
    public function __construct(private readonly ReflectionClass $class)
    {
    }

    public function getCreationMetadata(ApieContext $context): ItemHashmapMetadata
    {
        return new ItemHashmapMetadata($this->class);
    }

    public function getModificationMetadata(ApieContext $context): ItemHashmapMetadata
    {
        return new ItemHashmapMetadata($this->class);
    }
}
