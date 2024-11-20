<?php
namespace Apie\Core\Exceptions;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;

final class EntityNotFoundException extends ApieException implements HttpStatusCodeException
{
    /**
     * @param IdentifierInterface<EntityInterface> $identifier
     */
    public function __construct(IdentifierInterface $identifier)
    {
        parent::__construct(
            sprintf(
                "Entity '%s' with id '%s' is not found!",
                $identifier::getReferenceFor()->getShortName(),
                $identifier->toNative()
            )
        );
    }

    public function getStatusCode(): int
    {
        return 404;
    }
}
