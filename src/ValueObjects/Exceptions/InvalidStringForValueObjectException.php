<?php
namespace Apie\Core\ValueObjects\Exceptions;

use Apie\Core\Exceptions\ApieException;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\Utils;
use ReflectionClass;
use Throwable;

/**
 * Exception thrown by a value object that the input is not valid for a
 * value object.
 */
class InvalidStringForValueObjectException extends ApieException
{
    /**
     * @param ValueObjectInterface|ReflectionClass<object> $valueObject
     */
    public function __construct(string $input, ValueObjectInterface|ReflectionClass $valueObject, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                'Value "%s" is not valid for value object of type: %s',
                $input,
                Utils::getDisplayNameForValueObject($valueObject)
            ),
            0,
            $previous
        );
    }
}
