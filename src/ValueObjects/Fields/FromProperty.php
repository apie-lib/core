<?php
namespace Apie\Core\ValueObjects\Fields;

use Apie\Core\Attributes\Optional;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\Utils;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use UnitEnum;

/**
 * FieldInterface implementation that reads a property from an object with reflection.
 */
final class FromProperty implements FieldInterface
{
    private ReflectionProperty $property;

    public function __construct(ReflectionProperty $property)
    {
        $this->property = $property;
        $property->setAccessible(true);
    }

    public function getTypehint(): string
    {
        $type = $this->property->getType();
        if ($type instanceof ReflectionNamedType) {
            return ($type->allowsNull() ? $type->getName() : ($type->getName() . '|null'));
        }
        return (string) $type;
    }

    public function isOptional(): bool
    {
        return $this->property->hasDefaultValue()
            || !empty($this->property->getAttributes(Optional::class))
            || $this->property->getType()->allowsNull();
    }

    public function fromNative(ValueObjectInterface $instance, mixed $value): void
    {
        $type = $this->property->getType();
        if ($type instanceof ReflectionUnionType || $type instanceof ReflectionNamedType) {
            self::fillField($instance, Utils::toTypehint($type, $value));
            return;
        }
        throw new InvalidTypeException($type, 'ReflectionUnionType|ReflectionNamedType');
    }

    public function fillField(ValueObjectInterface $instance, mixed $value): void
    {
        $this->property->setValue($instance, $value);
    }

    public function fillMissingField(ValueObjectInterface $instance): void
    {
        if (!$this->isOptional()) {
            $type = $this->property->getType();
            if (null === $type || $type instanceof ReflectionIntersectionType) {
                throw new InvalidTypeException($type, 'ReflectionUnionType|ReflectionNamedType');
            }
            throw new InvalidTypeException('(missing value)', (string) $type);
        }
        if (!empty($this->property->getAttributes(Optional::class))) {
            return;
        }
        $this->property->setValue($instance, $this->property->getDefaultValue());
    }

    public function isInitialized(ValueObjectInterface $instance): bool
    {
        return $this->property->isInitialized($instance);
    }

    public function getValue(ValueObjectInterface $instance): mixed
    {
        return $this->property->getValue($instance);
    }

    public function toNative(ValueObjectInterface $instance): null|array|string|int|float|bool|UnitEnum
    {
        $value = $this->getValue($instance);
        return Utils::toNative($value);
    }
}
