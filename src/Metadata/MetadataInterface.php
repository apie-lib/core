<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;

interface MetadataInterface
{
    public function getHashmap(): ReflectionHashmap;
    public function getRequiredFields(): StringList;
    public function toScalarType(): ScalarType;
    public function getArrayItemType(): ?MetadataInterface;
}
