<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use DateTime;
use DateTimeInterface;
use ReflectionClass;
use Stringable;
use UnitEnum;

trait IsStringValueObject
{
    private string $internal;
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
            throw new InvalidStringForValueObjectException(get_debug_type($input), new ReflectionClass(self::class));
        }
        if (is_object($input) && !$input instanceof Stringable) {
            throw new InvalidStringForValueObjectException(get_debug_type($input), new ReflectionClass(self::class));
        }
        return new static((string) $input);
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
