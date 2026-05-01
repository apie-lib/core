<?php
namespace Apie\Core\Policies;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;

class BoundedContextPolicyProvider implements PolicyProviderInterface
{
    public function __construct(
        private readonly PolicyProviderHashmap $policiesByBoundedContext,
        private readonly FallbackPolicy $fallbackPolicy,
    ) {
    }

    public function getPolicyFor(ApieContext $apieContext, string $action): object
    {
        $boundedContext = $apieContext->getContext(ContextConstants::BOUNDED_CONTEXT_ID, false);
        if ($boundedContext === null) {
            return $this->fallbackPolicy;
        }

        // @phpstan-ignore nullCoalesce.expr        
        return $this->policiesByBoundedContext[$boundedContext]->getPolicyFor($apieContext, $action) ?? $this->fallbackPolicy;
    }
}
