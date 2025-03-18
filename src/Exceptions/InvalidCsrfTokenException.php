<?php
namespace Apie\Core\Exceptions;

/**
 * Exception thrown when an invalid CSRF token was provided in a form submit.
 */
final class InvalidCsrfTokenException extends ApieException implements HttpStatusCodeException
{
    public function __construct()
    {
        parent::__construct(
            sprintf('Invalid CSRF token or expired')
        );
    }

    public function getStatusCode(): int
    {
        return 419;
    }
}
