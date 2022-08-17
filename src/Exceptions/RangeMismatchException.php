<?php
namespace Apie\Core\Exceptions;

use Apie\Core\ValueObjects\Utils;

final class RangeMismatchException extends ApieException
{
    public function __construct(mixed $first, mixed $second)
    {
        parent::__construct(
            sprintf(
                '%s is higher than %s',
                Utils::displayMixedAsString($first),
                Utils::displayMixedAsString($second)
            )
        );
    }
}
