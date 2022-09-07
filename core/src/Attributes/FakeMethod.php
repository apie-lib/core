<?php
namespace Apie\Core\Attributes;

use Apie\Faker\Fakers\UseFakeMethodFaker;
use Attribute;

/**
 * Adding a FakeMethod attribute allows you to specify a static method to be used
 * to create a new instance with some random data.
 * @see UseFakeMethodFaker
 */
#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS)]
final class FakeMethod
{
    public string $methodName;

    public function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }
}
