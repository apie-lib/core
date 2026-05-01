<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Policies\PolicyManager;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Policy implements ApieContextAttribute
{
    /**
     * @param array<string, mixed> $additionalContext
     */
    public function __construct(
        public string $rule,
        public ?string $globalRule = null,
        public array $additionalContext = []
    ) {
    }

    public function applies(ApieContext $context): bool
    {
        $policyManager = $context->getContext(PolicyManager::class, false);
        $rule = $this->rule;
        if ($this->globalRule && !$context->hasContext(ContextConstants::RESOURCE)) {
            $rule = $this->globalRule;
        }
        if ($policyManager instanceof PolicyManager) {
            return $policyManager->allowed(
                $context->withMultipleContext($this->additionalContext),
                $rule
            );
        }

        return false;
    }
}
