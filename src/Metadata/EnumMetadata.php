<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;
use ReflectionEnum;

class EnumMetadata implements MetadataInterface
{
    public function __construct(private ReflectionEnum $enum)
    {
    }

    public function getHashmap(): MetadataFieldHashmap
    {
        return new MetadataFieldHashmap();
    }

    public function getRequiredFields(): StringList
    {
        return new StringList([]);
    }

    /** @return array<string|int, string|int> */
    public function getOptions(ApieContext $apieContext, bool $runtimeFilter = false): array
    {
        $cases = $this->enum->getCases();
        $result = [];
        foreach ($cases as $case) {
            if ($apieContext->appliesToContext($case, $runtimeFilter)) {
                $result[$case->getName()] = $this->enum->isBacked() ? $case->getBackingValue() : $case->getName();
            }
        }
        return $result;
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
