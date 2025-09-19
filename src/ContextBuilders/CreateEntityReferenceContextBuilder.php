<?php
namespace Apie\Core\ContextBuilders;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\ValueObjects\EntityReference;
use Apie\Core\ValueObjects\NonEmptyString;
use ReflectionClass;

class CreateEntityReferenceContextBuilder implements ContextBuilderInterface
{
    public function process(ApieContext $context): ApieContext
    {
        if (!$context->hasContext(ContextConstants::BOUNDED_CONTEXT_ID)
            || !$context->hasContext(ContextConstants::RESOURCE_NAME)
            || !$context->hasContext(ContextConstants::RESOURCE_ID)
        ) {
            return $context;
        }
        return $context
            ->withContext(
                EntityReference::class,
                new EntityReference(
                    new BoundedContextId($context->getContext(ContextConstants::BOUNDED_CONTEXT_ID)),
                    NonEmptyString::fromNative(
                        (new ReflectionClass($context->getContext(ContextConstants::RESOURCE_NAME)))->getShortName()
                    ),
                    NonEmptyString::fromNative($context->getContext(ContextConstants::RESOURCE_ID))
                )
            );
        ;
    }
}
