<?php
namespace Apie\Core\Exceptions;

use ReflectionClass;

class ClassCanNotBeFakedException extends ApieException
{
    public function __construct(ReflectionClass $class)
    {
        parent::__construct(sprintf('Class "%s" can not faked!', $class->name));
    }
}
