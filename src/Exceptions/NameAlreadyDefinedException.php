<?php


namespace Apie\Core\Exceptions;

class NameAlreadyDefinedException extends BadConfigurationException
{
    public function __construct(string $name)
    {
        parent::__construct('Name "' . $name . '" is already defined!');
    }
}
