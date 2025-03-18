<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemList;
use Apie\Core\Lists\ItemSet;
use Apie\Core\Metadata\ItemListMetadata;
use Apie\Core\Metadata\StrategyInterface;
use Apie\Core\Utils\HashmapUtils;
use ReflectionClass;

final class ItemListObjectStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return HashmapUtils::isList($class) || HashmapUtils::isSet($class);
    }

    /**
     * @param ReflectionClass<ItemList|ItemSet> $class
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
