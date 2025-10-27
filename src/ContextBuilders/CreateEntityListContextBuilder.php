<?php
namespace Apie\Core\ContextBuilders;

use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Core\Datalayers\Lists\EntityListInterface;

class CreateEntityListContextBuilder implements ContextBuilderInterface
{
    public function process(ApieContext $context): ApieContext
    {
        $apieDatalayer = $context->getContext(ApieDatalayer::class, false);
        $hashmap = $context->getContext(BoundedContextHashmap::class, false);
        if (!$context->hasContext(ContextConstants::BOUNDED_CONTEXT_ID)
            || !$context->hasContext(ContextConstants::RESOURCE_NAME)
            || $context->hasContext(ContextConstants::RESOURCE_ID)
            || $apieDatalayer === null
            || $hashmap === null
        ) {
            return $context;
        }

        $boundedContextId = new BoundedContextId($context->getContext(ContextConstants::BOUNDED_CONTEXT_ID));
        $resourceName = $context->getContext(ContextConstants::RESOURCE_NAME);
        $boundedContext = $hashmap[$boundedContextId->toNative()] ?? null;
        foreach ($boundedContext->resources ?? [] as $resource) {
            if ($resource->getShortName() === $resourceName || $resource->name === $resourceName) {
                return $context
                    ->withContext(
                        EntityListInterface::class,
                        $apieDatalayer->all($resource, $boundedContextId)
                    )
                    ->withContext(
                        ContextConstants::RESOURCE_NAME,
                        $resource->name
                    );
            }
        }
        return $context;
    }
}
