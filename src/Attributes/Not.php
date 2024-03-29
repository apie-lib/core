<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Attribute;

/**
 * Add this attribute to add a negative check.
 */
#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER|Attribute::TARGET_CLASS_CONSTANT)]
final class Not implements ApieContextAttribute
{
    public function __construct(private ApieContextAttribute $check)
    {
    }
    
    public function applies(ApieContext $context): bool
    {
        return !$this->check->applies($context);
    }
}
