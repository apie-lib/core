<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * This is used on classes, traits or interface to give a class a human readable description.
 * This can be used for LLM's or in the OpenAPI specification to describe a field better.
 */
#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER|Attribute::TARGET_CLASS_CONSTANT)]
final class Description
{
    public function __construct(public string $description)
    {
    }
}
