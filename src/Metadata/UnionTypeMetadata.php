<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;

class UnionTypeMetadata implements MetadataInterface
{
    /**
     * @var MetadataInterface[] $metadata
     */
    private array $metadata;

    public function __construct(MetadataInterface... $metadata)
    {
        $this->metadata = $metadata;
    }

    public function getHashmap(): ReflectionHashmap
    {
        $map = [];
        foreach ($this->metadata as $objectData) {
            foreach ($objectData->getHashmap() as $key => $value) {
                if (isset($map[$key])) {
                    // TODO make it an union type
                }
                $map[$key] = $value;
            }
        }
        return new ReflectionHashmap($map);
    }
    
    public function getRequiredFields(): StringList
    {
        $requiredFields = [];
        foreach ($this->metadata as $objectData) {
            $required = $objectData->getRequiredFields()->toArray();
            $requiredFields[] = array_combine($required, $required);
        }
        return new StringList($requiredFields ? array_intersect_key(...$requiredFields) : []);
    }

    public function toScalarType(): ScalarType
    {
        $current = null;
        foreach ($this->metadata as $objectData) {
            $type = $objectData->toScalarType();
            if ($current === null) {
                $current = $type;
            } elseif ($type !== $current) {
                return ScalarType::MIXED;
            }
        }
        return $current ?? ScalarType::MIXED;
    }

    public function getArrayItemType(): ?MetadataInterface
    {
        foreach ($this->metadata as $objectData) {
            $arrayPrototype = $objectData->getArrayItemType();
            if (!isset($arrayType) || $arrayType === $arrayPrototype) {
                $arrayType = $arrayPrototype;
            }
        }
        return $arrayType ?? null;
    }
}
