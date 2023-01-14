<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;
use ReflectionClass;

final class CompositeMetadata implements MetadataInterface
{
    private ?StringList $requiredFields = null;

    public function __construct(private readonly MetadataFieldHashmap $hashmap, private ?ReflectionClass $class = null)
    {
    }

    public function toClass(): ?ReflectionClass
    {
        return $this->class;
    }

    public function toScalarType(): ScalarType
    {
        return ScalarType::STDCLASS;
    }

    public function getHashmap(): MetadataFieldHashmap
    {
        return $this->hashmap;
    }

    public function getRequiredFields(): StringList
    {
        if (null === $this->requiredFields) {
            $required = [];
            foreach ($this->hashmap as $name => $field) {
                if ($field->isField() && $field->isRequired()) {
                    $required[] = $name;
                }
            }
            $this->requiredFields = new StringList($required);
        }
        return $this->requiredFields;
    }

    public function getArrayItemType(): ?MetadataInterface
    {
        return null;
    }
}
