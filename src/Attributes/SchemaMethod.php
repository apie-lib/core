<?php
namespace Apie\Core\Attributes;

use Attribute;
use Apie\SchemaGenerator\SchemaProviders\SchemaAttributeProvider;

/**
 * Adding a SchemaMethod attribute allows you to specify a static method to be used
 * to create the OpenAPI schema.
 * 
 * @see SchemaAttributeProvider
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
