<?php
namespace Apie\Core\Exceptions;

use ReflectionMethod;

final class MethodIsNotStaticException extends ApieException
{
    public function __construct(ReflectionMethod $method)
    {
        parent::__construct(sprintf('Class method "%s" is not static!', $method->getName()));
    }
}
