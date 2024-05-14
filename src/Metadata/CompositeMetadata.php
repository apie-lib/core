<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;
use Apie\Core\Metadata\Concerns\NoValueOptions;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;

/**
 * Composite value objects
 * - Implements ValueObjectInterface
 * - has CompositeValueObject trait
 * - is always mapped as an object
 */
final class CompositeMetadata implements MetadataInterface
{
    use NoValueOptions;

    private ?StringList $requiredFields = null;

    /**
     * @param ReflectionClass<ValueObjectInterface> $class
     */
    public function __construct(private readonly MetadataFieldHashmap $hashmap, private ?ReflectionClass $class = null)
    {
    }

    /**
     * @return ReflectionClass<ValueObjectInterface>
     */
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
