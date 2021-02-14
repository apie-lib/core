<?php


namespace Apie\Core\Exceptions;

use Apie\Core\Interfaces\ApiResourcePersisterInterface;
use Apie\Core\Interfaces\ApiResourceRetrieverInterface;

/**
 * Exception thrown if the persister or retriever did not return an instance of the resource we wanted.
 */
class InvalidReturnTypeOfApiResourceException extends ApieException
{
    /**
     * @param ApiResourceRetrieverInterface|ApiResourcePersisterInterface|null $retrieverOrPersister
     * @param string $identifier
     * @param string $expectedResource
     */
    public function __construct($retrieverOrPersister, string $identifier, string $expectedResource)
    {
        $message = 'I expect the class '
            . (is_null($retrieverOrPersister) ? '(null)' : get_class($retrieverOrPersister))
            . ' to return an instance of '
            . $expectedResource
            . ' but got '
            . $identifier;
        parent::__construct(500, $message);
    }
}
