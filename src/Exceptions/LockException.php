<?php
namespace Apie\Core\Exceptions;

/**
 * Exception thrown when a lock could not be acquired
 */
final class LockException extends ApieException implements HttpStatusCodeException
{
    public function __construct()
    {
        parent::__construct(
            'Could not create lock for resource!'
        );
    }

    public function getStatusCode(): int
    {
        return 419;
    }
}
