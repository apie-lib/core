<?php
namespace Apie\Core\Exceptions;

use Throwable;

final class ActionNotAllowedException extends ApieException implements HttpStatusCodeException
{
    public function __construct(?Throwable $previous = null)
    {
        $message = "Action not allowed!";
        if ($previous instanceof ActionNotAllowedException) {
            $message = $previous->getMessage();
        } elseif ($previous) {
            $message = 'Action not allowed. Reason: ' . $previous->getMessage();
        }
    
        parent::__construct($message, 0, $previous);
    }

    public function getStatusCode(): int
    {
        return 403;
    }
}
