<?php
namespace Apie\Core\ValueObjects\Interfaces;

use Apie\Core\Lists\StringSet;

interface LimitedOptionsInterface extends StringValueObjectInterface
{
    public static function getOptions(): StringSet;
}
