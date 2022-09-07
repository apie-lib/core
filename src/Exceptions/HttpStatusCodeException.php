<?php
namespace Apie\Core\Exceptions;

/**
 * Add this interface to tell to use a different HTTP status code.
 */
interface HttpStatusCodeException
{
    public function getStatusCode(): int;
}
