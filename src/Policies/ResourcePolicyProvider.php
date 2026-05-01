<?php
namespace Apie\Core\Policies;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Lists\ItemHashmap;

class ResourcePolicyProvider implements PolicyProviderInterface
{
    public function __construct(
        private readonly ItemHashmap $policyProviders,
        private readonly FallbackPolicy $fallbackPolicy
    ) {
    }

    public function getPolicyFor(ApieContext $apieContext, string $action): object
    {
        $resourceName = $apieContext->getContext(ContextConstants::RESOURCE_NAME, false);
        if ($resourceName === null) {
            return $this->fallbackPolicy;
        }
        if (class_exists($resourceName)) {
            $resourceName = (new \ReflectionClass($resourceName))->getShortName();
        }

        return $this->policyProviders[$resourceName] ?? $this->fallbackPolicy;
    }
}
