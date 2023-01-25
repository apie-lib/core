<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Attribute;

/**
 * Add this attribute to tell Apie how to index this entity.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class ProvideIndex
{
    public function __construct(public string $methodName)
    {
    }
}
