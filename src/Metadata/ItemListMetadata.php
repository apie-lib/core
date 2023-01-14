<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\ItemList;
use Apie\Core\Lists\StringList;
use ReflectionClass;

final class ItemListMetadata implements MetadataInterface
{
    /**
     * @param ReflectionClass<ItemList> $class
     */
    public function __construct(private readonly ReflectionClass $class)
    {
    }

    public function toClass(): ReflectionClass
    {
        return $this->class;
    }

    public function getHashmap(): MetadataFieldHashmap
    {
        return new MetadataFieldHashmap();
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
            $this->class->getMethod('offsetGet')->getReturnType()
        )->getCreationMetadata(
            new ApieContext()
        );
    }
}
