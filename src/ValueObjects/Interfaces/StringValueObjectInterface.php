<?php
namespace Apie\Core\ValueObjects\Interfaces;

use JsonSerializable;
use Stringable;

/**
 * Value objects that can be represented by strings.
 */
interface StringValueObjectInterface extends ValueObjectInterface, Stringable, JsonSerializable
{
    public function toNative(): string;
}
