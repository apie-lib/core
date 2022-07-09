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
use stdClass;

class ItemHashmap implements HashmapInterface
{
    protected stdClass $internal;
    protected array $internalArray = [];

    /** @var ReflectionType[] */
    private static $typeMapping = [];

    final public function __construct(array|stdClass $input = [])
    {
        $this->internal = new stdClass();
        foreach ($input as $key => $item) {
            $this->offsetSet($key, $item);
        }
    }

    public function count(): int
    {
        return count($this->internalArray);
    }

    public function toArray(): array
    {
        return $this->internalArray;
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->internalArray);
    }

    public function jsonSerialize(): stdClass
    {
        return $this->internal;
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->internalArray);
    }
    public function offsetGet(mixed $offset): mixed
    {
        if (!array_key_exists($offset, $this->internalArray)) {
            throw new IndexNotFoundException($offset);
        }
        return $this->internalArray[$offset];
    }

    protected function getType(): ReflectionType
    {
        $currentClass = static::class;
        if (!isset(self::$typeMapping[$currentClass])) {
            self::$typeMapping[$currentClass] = (new ReflectionClass($currentClass))->getMethod('offsetGet')->getReturnType();
        }
        return self::$typeMapping[$currentClass];
    }

    protected function offsetCheck(mixed $value): string
    {
        if ($value === null) { // append
            return count($this->internalArray);
        }
        return Utils::toString($value);
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
        $this->internal->{$offset} = $value;
        $this->internalArray[$offset] = $value;
    }
    public function offsetUnset(mixed $offset): void
    {
        $offset = Utils::toString($offset);
        if (array_key_exists($offset, $this->internalArray)) {
            unset($this->internalArray[$offset]);
            unset($this->internal->{$offset});
            return;
        }
        
        throw new IndexNotFoundException($offset);
    }
}
