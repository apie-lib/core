<?php
namespace Apie\Core\Exceptions;

use Apie\Core\ValueObjects\Utils;
use ReflectionNamedType;
use ReflectionUnionType;
use Throwable;

class InvalidTypeException extends ApieException
{
    private mixed $input;

    private string $expected;

    public function __construct(mixed $input, string $expected, ?Throwable $previous = null)
    {
        $this->input = $input;
        $this->expected;
        parent::__construct(
            sprintf(
                'Type %s is not expected, expected %s',
                Utils::displayMixedAsString($input),
                $expected
            ),
            0,
            $previous
        );
    }

    public static function chainException(self $previous): self
    {
        return new self($previous->input, $previous->expected, $previous);
    }

    public static function fromTypehint(mixed $input, ReflectionUnionType|ReflectionNamedType $typehint): self
    {
        return new self($input, $typehint->getName());
    }
}
