<?php
namespace Apie\Core\Policies;

use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\Concerns\UseContextKey;
use ReflectionException;
use ReflectionMethod;

class PolicyManager
{
    use UseContextKey;
    public function __construct(
        private readonly PolicyProviderInterface $policyProvider,
        private readonly bool $defaultAllow = true,
    ) {
    }

    public function allowed(ApieContext $apieContext, string $action, ?bool $enabledOnMissingRule = null): bool
    {
        $policy = $this->policyProvider->getPolicyFor($apieContext, $action);
        if (is_callable([$policy, $action])) {
            try {
                $reflection = new ReflectionMethod($policy, $action);
                $args = [];
                foreach ($reflection->getParameters() as $parameter) {
                    try {
                        $contextKey = $this->getContextKey($apieContext, $parameter, false);
                        $args[] = $apieContext->getContext($contextKey);
                    } catch (\Throwable $err) {
                        return $enabledOnMissingRule ?? $this->defaultAllow;
                    }
                }
                return $policy->{$action}(...$args) ?? $enabledOnMissingRule ?? $this->defaultAllow;
            } catch (ReflectionException $err) {
                // for __call methods we can't do reflection, so we just call the method and hope for the best
                return call_user_func([$policy, $action]) ?? $enabledOnMissingRule ?? $this->defaultAllow;
            }
        }
        return $enabledOnMissingRule ?? $this->defaultAllow;
    }
}
