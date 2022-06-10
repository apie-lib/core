<?php
namespace Apie\Core\ValueObjects;

use UnitEnum;

interface ValueObjectInterface
{
    public static function fromNative(mixed $input): self;
    public function toNative(): array|string|int|float|bool|UnitEnum;
}
