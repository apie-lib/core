<?php
namespace Apie\Core\Attributes;

use Apie\Serializer\Lists\SerializedHashmap;
use Apie\Serializer\Lists\SerializedList;
use Attribute;

/**
 * This is used on classes, traits or interface to give a class a human readable examples.
 * This can be used for LLM's or in the OpenAPI specification to describe a field better.
 */
#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER|Attribute::TARGET_CLASS_CONSTANT)]
final class ExampleValue
{
    public function __construct(
        public readonly string|int|float|bool|SerializedList|SerializedHashmap|null $example,
        public readonly ?string $name = null,
    ) {
    }

    /**
     * @return array<string, mixed>|string|int|float|bool|null
     */
    public function toExample(): string|int|float|bool|array|null
    {
        if ($this->example instanceof SerializedList || $this->example instanceof SerializedHashmap) {
            return $this->example->toArray();
        }
        return $this->example;
    }
}
