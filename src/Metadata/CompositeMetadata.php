<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;

final class CompositeMetadata implements MetadataInterface {
    public function __construct(private readonly ReflectionHashmap $hashmap, private readonly StringList $requiredFields)
    {
    }

    public function toScalarType(): ScalarType
    {
        return ScalarType::STDCLASS;
    }

    public function getHashmap(): ReflectionHashmap
    {
        return $this->hashmap;
    }

    public function getRequiredFields(): StringList
    {
        return $this->requiredFields;
    }

    public function getArrayItemType(): ?MetadataInterface
    {
        return null;
    }
}