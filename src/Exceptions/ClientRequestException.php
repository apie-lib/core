<?php
namespace Apie\Core\Exceptions;

use Throwable;

/**
 * Something went wrong with the client request, but it was not clear how it could be mapped.
 */
final class ClientRequestException extends ApieException implements HttpStatusCodeException
{
    public function __construct(Throwable $previous)
    {
        parent::__construct(
            'Unknown client request error: ' . $previous->getMessage(),
            0,
            $previous
        );
    }

    public function getStatusCode(): int
    {
        return 400;
    }
}
