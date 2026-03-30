<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\IdentifierUtils;

class EntityReference extends SnowflakeIdentifier
{
    final public function __construct(
        protected BoundedContextId $boundedContextId,
        protected NonEmptyString $entityClass,
        protected NonEmptyString $id
    ) {
    }

    public static function createFromContext(ApieContext $context): ?static
    {
        // in POST request this is the case if you make a new entity
        $createdResource = $context->getContext(ContextConstants::RESOURCE, false);
        if (!$context->hasContext(ContextConstants::RESOURCE_ID) && $createdResource instanceof EntityInterface) {
            $context = $context->withContext(
                ContextConstants::RESOURCE_ID,
                $createdResource->getId()->toNative()
            );
        }

        if (!$context->hasContext(ContextConstants::BOUNDED_CONTEXT_ID)
            || !$context->hasContext(ContextConstants::RESOURCE_NAME)
            || !$context->hasContext(ContextConstants::RESOURCE_ID)
        ) {
            return null;
        }
        $boundedContextId = new BoundedContextId($context->getContext(ContextConstants::BOUNDED_CONTEXT_ID));
        $resourceName = $context->getContext(ContextConstants::RESOURCE_NAME);

        $hashmap = $context->getContext(BoundedContextHashmap::class);
        $boundedContext = $hashmap[$boundedContextId->toNative()] ?? null;
        foreach ($boundedContext->resources ?? [] as $resource) {
            if ($resource->getShortName() === $resourceName || $resource->name === $resourceName) {
                $id = IdentifierUtils::entityClassToIdentifier($resource)
                    ->getMethod('fromNative')
                    ->invoke(null, $context->getContext(ContextConstants::RESOURCE_ID));
                return new static(
                    $boundedContextId,
                    NonEmptyString::fromNative($resource->getShortName()),
                    NonEmptyString::fromNative($id->toNative())
                );
            }
        }

        return null;
    }

    protected static function getSeparator(): string
    {
        return '/';
    }

    public function getBoundedContextId(): BoundedContextId
    {
        return $this->boundedContextId;
    }

    public function getEntityClass(): NonEmptyString
    {
        return $this->entityClass;
    }

    public function getId(): NonEmptyString
    {
        return $this->id;
    }

    public function resolve(ApieContext $apieContext): ?object
    {
        $dataLayer = $apieContext->getContext(ApieDatalayer::class, false);
        $hashmap = $apieContext->getContext(BoundedContextHashmap::class);
        $boundedContext = $hashmap[$this->boundedContextId->toNative()] ?? null;
        foreach ($boundedContext->resources as $resource) {
            if ($resource->getShortName() === $this->entityClass->toNative()) {
                $id = IdentifierUtils::entityClassToIdentifier($resource)
                    ->getMethod('fromNative')
                    ->invoke(null, $this->id->toNative());
                return $dataLayer->find($id, $this->boundedContextId);
            }
        }
        return null;
    }
}
