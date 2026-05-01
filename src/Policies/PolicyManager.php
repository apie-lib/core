<?php
namespace Apie\Core\Policies;

use Apie\Core\Context\ApieContext;

class PolicyManager
{
    public function __construct(
        private readonly PolicyProviderInterface $policyProvider,
        private readonly bool $defaultAllow = true,
    ) {
    }

    public function allowed(ApieContext $apieContext, string $action): bool
    {
        $policy = $this->policyProvider->getPolicyFor($apieContext, $action);

        if (is_callable([$policy, $action])) {
            // TODO: use reflection to determine the arguments to pass here
            return $policy->{$action}();
        }
        return $this->defaultAllow;
    }
}
