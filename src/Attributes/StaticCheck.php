<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Attribute;

/**
 * Checks all attributes in static context checks. A static context check is always
 * done and is for example used to generate the correct routing rules.
 *
 * A runtime context check is for example if it requires permission to modify something.
 */
#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER|Attribute::TARGET_CLASS_CONSTANT)]
final class StaticCheck implements ApieContextAttribute
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
