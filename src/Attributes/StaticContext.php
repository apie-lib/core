<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Attribute;

/**
 * Does static context changes. This is used for example to filter operations on a global level, for example when
 * URL's are being generated.
 */
#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER|Attribute::TARGET_CLASS_CONSTANT)]
final class StaticContext implements ApieContextAttribute
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
