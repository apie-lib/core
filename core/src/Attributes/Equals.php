<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Attribute;

/**
 * Add this attribute to tell ApieContext that it should only be called if a specific context is set and is equal to
 * the value provided.
 */
#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER|Attribute::TARGET_CLASS_CONSTANT)]
final class Equals implements ApieContextAttribute
{
    public function __construct(public string $instance, public mixed $comparison)
    {
    }
    
    public function applies(ApieContext $context): bool
    {
        return $context->hasContext($this->instance) && $context->getContext($this->instance) === $this->comparison;
    }
}
