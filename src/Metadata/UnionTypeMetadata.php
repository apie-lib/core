<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;
use Apie\Core\Metadata\Fields\OptionalField;
use ReflectionClass;

class UnionTypeMetadata implements NullableMetadataInterface
{
    /**
     * @var MetadataInterface[] $metadata
     */
    private array $metadata;

    public function __construct(MetadataInterface... $metadata)
    {
        $this->metadata = $metadata;
    }

    public function toClass(): ?ReflectionClass
    {
        $first = true;
        $class = null;
        foreach ($this->metadata as $metadata) {
            $current = $metadata->toClass();
            if (null === $current) {
                return null;
            }
            if ($first) {
                $class = $metadata->toClass();
                $first = false;
            }
            if ($current->name !== $class->name) {
                return null;
            }
        }
        return null;
    }

    /**
     * @return MetadataInterface[]
     */
    public function getTypes(): array
    {
        return $this->metadata;
    }

    public function getHashmap(): MetadataFieldHashmap
    {
        $map = [];
        foreach ($this->metadata as $objectData) {
            foreach ($objectData->getHashmap() as $key => $value) {
                if (isset($map[$key])) {
                    $value = new OptionalField($value, $map[$key]);
                }
                $map[$key] = $value;
            }
        }
        return new MetadataFieldHashmap($map);
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

    public function toScalarType(bool $ignoreNull = false): ScalarType
    {
        $current = null;
        foreach ($this->metadata as $objectData) {
            $type = $objectData->toScalarType($ignoreNull);
            if ($ignoreNull && $type === ScalarType::NULL) {
                continue;
            }
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
