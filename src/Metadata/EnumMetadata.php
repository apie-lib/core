<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;
use ReflectionEnum;

class EnumMetadata implements MetadataInterface
{
    public function __construct(private ReflectionEnum $enum)
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
        return $this->enum->isBacked() ? ScalarType::from((string) $this->enum->getBackingType()) : ScalarType::STRING;
    }

    public function getArrayItemType(): ?MetadataInterface
    {
        return null;
    }
}
