<?php
namespace Apie\Core\ValueObjects\Interfaces;

use JsonSerializable;
use Stringable;

/**
 * Interface for value objects that can be expressed in a regular expression.
 */
interface HasRegexValueObjectInterface extends ValueObjectInterface, Stringable, JsonSerializable
{
    public function toNative(): string;
    public static function getRegularExpression(): string;
}
