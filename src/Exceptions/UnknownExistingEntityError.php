<?php
namespace Apie\Core\Exceptions;

use Apie\Core\Entities\EntityInterface;
use ReflectionClass;

final class UnknownExistingEntityError extends ApieException implements HttpStatusCodeException
{
    public function __construct(EntityInterface $entity)
    {
        parent::__construct(
            sprintf(
                "Entity '%s' with id '%s' is not found in the persistence layer!",
                (new ReflectionClass($entity))->getShortName(),
                $entity->getId()->toNative()
            )
        );
    }

    public function getStatusCode(): int
    {
        return 409;
    }
}
