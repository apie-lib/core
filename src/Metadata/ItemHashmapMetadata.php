<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\StringList;
use ReflectionClass;

final class ItemHashmapMetadata implements MetadataInterface
{
    /**
     * @param ReflectionClass<ItemHashmap> $class
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
        return ScalarType::STDCLASS;
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
