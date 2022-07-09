<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * Adding a FakeMethod attribute allows you to specify a static method to be used
 * to create a new instance with some random data.
 */
#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS)]
class FakeMethod
{
    public string $methodName;

    public function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }
}
