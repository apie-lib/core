<?php
namespace Apie\Core\ContextBuilders;

use Apie\Core\Context\ApieContext;
use Apie\Core\ValueObjects\EntityReference;

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
