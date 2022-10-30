<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;

interface MetadataInterface
{
    public function getHashmap(): MetadataFieldHashmap;
    public function getRequiredFields(): StringList;
    public function toScalarType(): ScalarType;
    public function getArrayItemType(): ?MetadataInterface;
}
