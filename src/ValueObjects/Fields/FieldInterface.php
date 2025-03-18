<?php
namespace Apie\Core\ValueObjects\Fields;

use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use UnitEnum;

interface FieldInterface
{
    public function fillField(ValueObjectInterface $instance, mixed $value): void;

    public function fillMissingField(ValueObjectInterface $instance): void;

    public function fromNative(ValueObjectInterface $instance, mixed $value): void;

    public function getValue(ValueObjectInterface $instance): mixed;

    /**
     * @return mixed[]|string|int|float|bool|UnitEnum|null
     */
    public function toNative(ValueObjectInterface $instance): array|string|int|float|bool|UnitEnum|null;

    public function isInitialized(ValueObjectInterface $instance): bool;

    public function isOptional(): bool;

    public function getTypehint(): string;
}
