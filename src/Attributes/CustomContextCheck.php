<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Attribute;

/**
 * Add this attribute for any custom behaviour by providing a class implementing ApieContextAttribute.
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER)]
class CustomContextCheck implements ApieContextAttribute
{
    public function __construct(private ApieContextAttribute $check)
    {
    }
    
    public function applies(ApieContext $context): bool
    {
        return $this->check->applies($context);
    }
}
