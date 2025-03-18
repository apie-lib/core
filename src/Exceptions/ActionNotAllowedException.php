<?php
namespace Apie\Core\Exceptions;

use Throwable;

final class ActionNotAllowedException extends ApieException implements HttpStatusCodeException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct($previous ? ('Action not allowed. Reason: ' . $previous->getMessage()) : "Action not allowed!", 0, $previous);
    }

    public function getStatusCode(): int
    {
        return 403;
    }
}
