<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * Add this attribute to tell Apie how it should name this class without namespace.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class ResourceName
{
    public function __construct(public string $name)
    {
    }
}
