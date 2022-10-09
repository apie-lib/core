<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;

final class ScalarMetadata implements MetadataInterface
{
    public function __construct(private ScalarType $type)
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
        return $this->type;
    }

    public function getArrayItemType(): ?MetadataInterface
    {
        if ($this->type === ScalarType::ARRAY || $this->type === ScalarType::STDCLASS) {
            return new ScalarMetadata(ScalarType::MIXED);
        }
        return null;
    }
}
