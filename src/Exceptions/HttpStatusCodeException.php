<?php
namespace Apie\Core\Exceptions;

interface HttpStatusCodeException {
    public function getStatusCode(): int;
}