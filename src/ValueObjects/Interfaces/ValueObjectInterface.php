<?php
namespace Apie\Core\ValueObjects\Interfaces;

use UnitEnum;

interface ValueObjectInterface
{
    /**
     * @return static
     */
    public static function fromNative(mixed $input): self;
    /**
     * @return array<string|int, mixed>|string|int|float|bool|UnitEnum|null
     */
    public function toNative(): array|string|int|float|bool|UnitEnum|null;
}
