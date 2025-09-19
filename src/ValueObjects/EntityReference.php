<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Core\IdentifierUtils;

class EntityReference extends SnowflakeIdentifier
{
    public function __construct(
        private BoundedContextId $boundedContextId,
        private NonEmptyString $entityClass,
        private NonEmptyString $id
    ) {
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
