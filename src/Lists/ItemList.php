<?php
namespace Apie\Core\Lists;

use Apie\Core\Exceptions\IndexNotFoundException;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\TypeUtils;
use Apie\Core\ValueObjects\Utils;
use ArrayIterator;
use Iterator;
use ReflectionClass;
use ReflectionType;

class ItemList implements ItemListInterface
{
    protected array $internal = [];

    /** @var ReflectionType[] */
    private static $typeMapping = [];

    final public function __construct(array $input = [])
    {
        foreach ($input as $item) {
            $this->offsetSet(null, $item);
        }
    }

    public function count(): int
    {
        return count($this->internal);
    }

    public function toArray(): array
    {
        return $this->internal;
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->internal);
    }

    public function jsonSerialize(): array
    {
        return $this->internal;
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->internal);
    }
    public function offsetGet(mixed $offset): mixed
    {
        if (!array_key_exists($offset, $this->internal)) {
            throw new IndexNotFoundException($offset);
        }
        return $this->internal[$offset];
    }

    protected function getType(): ReflectionType
    {
        $currentClass = static::class;
        if (!isset(self::$typeMapping[$currentClass])) {
            self::$typeMapping[$currentClass] = (new ReflectionClass($currentClass))->getMethod('offsetGet')->getReturnType();
        }
        return self::$typeMapping[$currentClass];
    }

    protected function offsetCheck(mixed $value): int
    {
        if ($value === null) { // append
            return count($this->internal);
        }
        $value = Utils::toInt($value);
        if ($value < 0) {
            throw new IndexNotFoundException($value);
        }
        // we check if null is allowed. If it is allowed we accept the current offset as it will expand the array.
        if ($value > count($this->internal) && !TypeUtils::matchesType($this->getType(), null)) {
            throw new IndexNotFoundException($value);
        }
        return $value;
    }

    protected function typeCheck(mixed $value): void
    {
        $type = $this->getType();
        if (!TypeUtils::matchesType($type, $value)) {
            throw new InvalidTypeException($value, $type->__toString());
        }
    }
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $offset = $this->offsetCheck($offset);
        $this->typeCheck($value);
        $this->internal[$offset] = $value;
    }
    public function offsetUnset(mixed $offset): void
    {
        $offset = Utils::toInt($offset);
        // a value can only be deleted if it is the last item in the array or if null is allowed
        if (($offset + 1) === count($this->internal)) {
            array_pop($this->internal);
            return;
        }
        if (TypeUtils::matchesType($this->getType(), null) && $offset >= 0 && $offset < count($this->internal)) {
            $this->internal[$offset] = null;
            return;
        }
        throw new IndexNotFoundException($offset);
    }
}
