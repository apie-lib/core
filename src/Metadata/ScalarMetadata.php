<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Dto\ValueOption;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;
use Apie\Core\Lists\ValueOptionList;
use ReflectionClass;

final class ScalarMetadata implements MetadataInterface
{
    public function __construct(private ScalarType $type)
    {
    }

    public function getValueOptions(ApieContext $context, bool $runtimeFilter = false): ?ValueOptionList
    {
        if ($this->type === ScalarType::BOOLEAN) {
            return new ValueOptionList([
                new ValueOption('True', true),
                new ValueOption('False', false),
            ]);
        }
        if ($this->type === ScalarType::NULL) {
            return new ValueOptionList([
                new ValueOption('(null)', null)
            ]);
        }
        return null;
    }

    public function toClass(): ?ReflectionClass
    {
        return null;
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
