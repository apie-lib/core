<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * Adding a SchemaMethod attribute allows you to specify a static method to be used
 * to create the OpenAPI schema.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class SchemaMethod
{
    public string $methodName;

    public function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }
}
