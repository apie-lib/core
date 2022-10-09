<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;
use ReflectionClass;

class ValueObjectMetadata implements MetadataInterface
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

    public function getHashmap(): ReflectionHashmap
    {
        return $this->getNativeType()->getHashmap();
    }

    public function getRequiredFields(): StringList
    {
        return $this->getNativeType()->getRequiredFields();
    }

    public function toScalarType(): ScalarType
    {
        return $this->getNativeType()->toScalarType();
    }

    public function getArrayItemType(): ?MetadataInterface
    {
        return $this->getNativeType()->getArrayItemType();
    }
}