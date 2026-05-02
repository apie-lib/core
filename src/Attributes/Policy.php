<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Policies\PolicyManager;

final class Policy implements ApieContextAttribute
{
    /**
     * @param array<string, mixed> $additionalContext
     */
    public function __construct(
        public string $rule,
        public ?string $globalRule = null,
        public ?bool $enabledOnMissingRule = null,
        public array $additionalContext = []
    ) {
    }

    public function applies(ApieContext $context): bool
    {
        $policyManager = $context->getContext(PolicyManager::class, false);
        $resource = $context->getContext(ContextConstants::RESOURCE, false);
        $rule = $this->rule;
        if ($this->globalRule && !$resource) {
            $rule = $this->globalRule;
        }
    
        $context = $context->withMultipleContext($this->additionalContext);
        // this makes it possible to write just the entity as a function argument in a policy class.
        if ($resource && !$context->hasContext(get_debug_type($resource))) {
            $context = $context
                ->registerInstance($resource)
                ->withContext(get_debug_type($resource), $resource);
        }
        
        if ($policyManager instanceof PolicyManager) {
            return $policyManager->allowed(
                $context,
                $rule,
                $this->enabledOnMissingRule
            );
        }

        return $this->enabledOnMissingRule ?? false;
    }
}
