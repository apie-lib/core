<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Attribute;

/**
 * Add this attribute that succeeds if any succeed. Adding no arguments is failure.
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER|Attribute::TARGET_CLASS_CONSTANT)]
class AnyApplies implements ApieContextAttribute
{
    /** @var ApieContextAttribute[] */
    private array $checks;

    public function __construct(ApieContextAttribute... $checks)
    {
        $this->checks = $checks;
    }
    
    public function applies(ApieContext $context): bool
    {
        foreach ($this->checks as $check) {
            if ($check->applies($context)) {
                return true;
            }
        }
        return false;
    }
}
