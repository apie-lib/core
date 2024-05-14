<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;
use Apie\Core\Lists\ValueOptionList;
use ReflectionClass;

interface MetadataInterface
{
    public function getHashmap(): MetadataFieldHashmap;
    public function getRequiredFields(): StringList;
    public function toScalarType(): ScalarType;
    public function getArrayItemType(): ?MetadataInterface;
    public function getValueOptions(ApieContext $context, bool $runtimeFilter = false): ?ValueOptionList;
    /**
     * @return ReflectionClass<object>|null
     */
    public function toClass(): ?ReflectionClass;
}
