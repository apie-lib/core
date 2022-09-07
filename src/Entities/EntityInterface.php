<?php
namespace Apie\Core\Entities;

use Apie\Core\Identifiers\IdentifierInterface;

/**
 * All Apie entities should implement this interface to tell Apie this is an entity.
 */
interface EntityInterface
{
    /**
     * @return IdentifierInterface<static>
     */
    public function getId(): IdentifierInterface;
}
