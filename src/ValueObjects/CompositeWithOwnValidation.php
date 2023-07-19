<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;

/**
 * Marker interface for composite values that validation/serialization is done by the composite value object
 * and not done by the regular serialization. This is needed if properties depend on each other for validation.
 */
interface CompositeWithOwnValidation extends ValueObjectInterface
{
    /** @return array<string|int, mixed> */
    public function toNative(): array;
}
