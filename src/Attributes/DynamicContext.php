<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Attribute;

/**
 * Does dynamic context changes. This should be used to filter out actions that should not be applied in the current
 * context, for example, you need to be logged in to do this action.
 */
#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER|Attribute::TARGET_CLASS_CONSTANT)]
final class DynamicContext implements ApieContextAttribute
{
    /**
     * @var ApieContextAttribute[]
     */
    private array $checks;

    public function __construct(ApieContextAttribute... $checks)
    {
        $this->checks = $checks;
    }
    
    public function applies(ApieContext $context): bool
    {
        foreach ($this->checks as $check) {
            if (!$check->applies($context)) {
                return false;
            }
        }
        return true;
    }
}
