<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;
use Apie\Core\Lists\ItemList;
use ReflectionClass;

final class ItemListMetadata implements MetadataInterface
{
    /**
     * @param ReflectionClass<ItemList> $class
     */
    public function __construct(private readonly ReflectionClass $class)
    {
    }

    public function getHashmap(): ReflectionHashmap
    {
        return new ReflectionHashmap([]);
    }
    public function getRequiredFields(): StringList
    {
        return new StringList([]);
    }
    public function toScalarType(): ScalarType
    {
        return ScalarType::ARRAY;
    }
    public function getArrayItemType(): ?MetadataInterface
    {
        return MetadataFactory::getMetadataStrategyForType(
            $this->class->getMethod('__offsetGet')->getReturnType()
        )->getCreationMetadata(
                new ApieContext()
            );
    }
}
