<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;

class ValueObjectMetadata implements NullableMetadataInterface
{
    /**
     * @param ReflectionClass<ValueObjectInterface> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    public function getNativeType(): MetadataInterface
    {
        $method = $this->class->getMethod('toNative');
        return MetadataFactory::getCreationMetadata($method->getReturnType(), new ApieContext());
    }

    public function getHashmap(): MetadataFieldHashmap
    {
        return $this->getNativeType()->getHashmap();
    }

    public function getRequiredFields(): StringList
    {
        return $this->getNativeType()->getRequiredFields();
    }

    public function toScalarType(bool $ignoreNull = false): ScalarType
    {
        return $this->getNativeType()->toScalarType($ignoreNull);
    }

    public function getArrayItemType(): ?MetadataInterface
    {
        return $this->getNativeType()->getArrayItemType();
    }
}
