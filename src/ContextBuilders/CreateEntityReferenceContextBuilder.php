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
        $ref = EntityReference::createFromContext($context);
        if ($ref === null) {
            return $context;
        }
        return $context
            ->withContext(
                EntityReference::class,
                $ref
            );
        ;
    }
}
