<?php
namespace Apie\Core\Exceptions;

class HttpNotFoundException extends ApieException implements HttpStatusCodeException
{
    public function __construct(string $message = "Not Found")
    {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return 404;
    }
}
