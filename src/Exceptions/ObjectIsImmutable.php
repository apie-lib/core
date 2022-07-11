<?php
namespace Apie\Core\Exceptions;

use Apie\Core\ValueObjects\Utils;

class ObjectIsImmutable extends ApieException
{
    public function __construct(object $object)
    {
        parent::__construct(
            sprintf(
                'Object %s is immutable and can not be altered',
                Utils::displayMixedAsString($object)
            )
        );
    }
}
