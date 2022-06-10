<?php
namespace Apie\Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class FakeMethod
{
    public string $methodName;

    public function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }
}