<?php
namespace Apie\Core\Exceptions;

class WrongContentTypeException extends ApieException
{
    public function __construct(string $contentType)
    {
        parent::__construct(sprintf('Invalid content type: "%s"', $contentType));
    }
}
