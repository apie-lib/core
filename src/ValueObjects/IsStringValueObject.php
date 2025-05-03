<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\TypeUtils;
use Apie\Core\Utils\ConverterUtils;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use DateTime;
use DateTimeInterface;
use ReflectionClass;
use ReflectionType;
use Stringable;
use UnitEnum;

trait IsStringValueObject
{
    private string $internal;
    private static bool $hasStringConstructor;
    private static ReflectionType $constructorType;

    public function __construct(string|int|float|bool|Stringable $input)
    {
        $input = $this->convert((string) $input);
        static::validate($input);
        $this->internal = $input;
    }

    public static function fromNative(mixed $input): self
    {
        if (gettype($input) == 'boolean') {
            $input = $input ? 'true' : 'false';
        }
        if ($input instanceof ValueObjectInterface) {
            $input = $input->toNative();
        }
        if ($input instanceof DateTimeInterface) {
            $input = $input->format(DateTime::ATOM);
        }
        if ($input instanceof UnitEnum) {
            $input = $input->value;
        }
        if (is_array($input)) {
            throw new InvalidTypeException($input, (new ReflectionClass(self::class))->getShortName());
        }
        if (is_object($input) && !$input instanceof Stringable) {
            throw new InvalidTypeException(
                $input,
                (new ReflectionClass(self::class))->getShortName()
            );
        }
        $class = new ReflectionClass(static::class);
        if (!$class->isInstantiable()) {
            self::validate((string) $input);
            return new self((string) $input);
        }
        if (self::toValidConstructorArgument($input)) {
            // @phpstan-ignore new.static
            return new static($input);
        }
        $instance = (new ReflectionClass(static::class))->newInstanceWithoutConstructor();
        $instance->internal = (string) $input;

        return $instance;
    }

    private static function toValidConstructorArgument(mixed& $input): bool
    {
        if (!isset(self::$hasStringConstructor)) {
            $refl = (new ReflectionClass(static::class));
            $constructor = $refl->getConstructor();
            $arguments = $constructor?->getParameters() ?? [];
            self::$hasStringConstructor = count($arguments) === 1;
        }
        if (!self::$hasStringConstructor) {
            return false;
        };
        if (!isset(self::$constructorType)) {
            $refl = (new ReflectionClass(static::class));
            $constructor = $refl->getConstructor();
            $arguments = $constructor?->getParameters() ?? [];
            self::$constructorType = reset($arguments)->getType() ?? ReflectionTypeFactory::createReflectionType('mixed');
        }
        if (TypeUtils::matchesType(self::$constructorType, $input)) {
            return true;
        }
        $input = ConverterUtils::dynamicCast(
            $input,
            self::$constructorType
        );
        return true;
    }

    public function toNative(): string
    {
        return $this->internal;
    }

    public function __toString(): string
    {
        return $this->toNative();
    }

    public function jsonSerialize(): string
    {
        return $this->toNative();
    }
    
    public static function validate(string $input): void
    {
    }

    protected function convert(string $input): string
    {
        return $input;
    }
}
