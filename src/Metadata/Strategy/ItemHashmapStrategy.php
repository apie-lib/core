<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Metadata\ItemHashmapMetadata;
use Apie\Core\Metadata\StrategyInterface;
use Apie\Core\Utils\HashmapUtils;
use ReflectionClass;

final class ItemHashmapStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return HashmapUtils::isHashmap($class);
    }

    /**
     * @param ReflectionClass<ItemHashmap> $class
     */
    public function __construct(private readonly ReflectionClass $class)
    {
    }

    public function getCreationMetadata(ApieContext $context): ItemHashmapMetadata
    {
        return new ItemHashmapMetadata($this->class, true);
    }

    public function getModificationMetadata(ApieContext $context): ItemHashmapMetadata
    {
        return new ItemHashmapMetadata($this->class, true);
    }

    public function getResultMetadata(ApieContext $context): ItemHashmapMetadata
    {
        return new ItemHashmapMetadata($this->class, false);
    }
}
