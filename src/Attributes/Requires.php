<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Attribute;

/**
 * Add this attribute to tell ApieContext that it should only be called if a specific context is set. The value
 * does not matter.
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER|Attribute::TARGET_CLASS_CONSTANT)]
class Requires implements ApieContextAttribute
{
    public function __construct(public string $instance)
    {
    }
    
    public function applies(ApieContext $context): bool
    {
        return $context->hasContext($this->instance);
    }
}
