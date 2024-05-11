<?php
namespace Apie\Core\Lists;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Exceptions\IndexNotFoundException;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\Exceptions\ObjectIsEmpty;
use Apie\Core\Exceptions\ObjectIsImmutable;
use Apie\Core\TypeUtils;
use Apie\Core\ValueObjects\Utils;
use ArrayIterator;
use Iterator;
use ReflectionClass;
use ReflectionType;
use Throwable;

/**
 * @template T
 * @implements ItemListInterface<T>
 */
class ItemSet implements ItemListInterface
{
    /**
     * @var array<int, T>
     */
    protected array $internal = [];

    /** @var ReflectionType[] */
    private static $typeMapping = [];

    protected bool $mutable = true;

    /**
     * @param array<int|string, T> $input
     */
    final public function __construct(array $input = [])
    {
        $oldMutable = $this->mutable;
        $this->mutable = true;
        foreach ($input as $item) {
            $this->offsetSet(null, $item);
        }
        $this->mutable = $oldMutable;
    }

    public function count(): int
    {
        return count($this->internal);
    }

    /**
     * @return T
     */
    public function first(): mixed
    {
        if (empty($this->internal)) {
            throw ObjectIsEmpty::createForList();
        }
        return reset($this->internal);
    }

    /**
     * @return array<int, T>
     */
    public function toArray(): array
    {
        return array_values($this->internal);
    }

    /**
     * @return Iterator<int, T>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator(array_values($this->internal));
    }

    /**
     * @return array<int, T>
     */
    public function jsonSerialize(): array
    {
        return array_values($this->internal);
    }

    public function offsetExists(mixed $offset): bool
    {
        $offset = $this->offsetCheck($offset);
        return array_key_exists($offset, $this->internal);
    }


    /**
     * @return T
     */
    public function offsetGet(mixed $offset): mixed
    {
        $offset = $this->offsetCheck($offset);
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

    protected function offsetCheck(mixed $value): string
    {
        if (is_array($value)) {
            return 'array,' . json_encode($value);
        }
        try {
            return get_debug_type($value) . ',' . Utils::toString($value);
        } catch (Throwable) {
            if ($value instanceof EntityInterface) {
                return get_debug_type($value) . ',' . $value->getId()->toNative();
            }
            return get_debug_type($value) . ',' . spl_object_hash($value);
        }
    }

    protected function typeCheck(mixed $value): void
    {
        if (static::class === ItemSet::class) {
            return;
        }
        $type = $this->getType();
        if (!TypeUtils::matchesType($type, $value)) {
            throw new InvalidTypeException($value, $type->__toString());
        }
    }

    /**
     * @return self<T>
     */
    public function append(mixed $value): self
    {
        $this->typeCheck($value);
        $returnValue = $this->mutable ? $this : clone $this;
        $returnValue->internal[$returnValue->offsetCheck($value)] = $value;

        return $returnValue;
    }

    /**
     * @param T $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$this->mutable) {
            throw new ObjectIsImmutable($this);
        }
        $offset = $this->offsetCheck($value);
        $this->typeCheck($value);
        $this->internal[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        if (!$this->mutable) {
            throw new ObjectIsImmutable($this);
        }
        $offset = $this->offsetCheck($offset);
        if (!array_key_exists($offset, $this->internal)) {
            throw new IndexNotFoundException($offset);
        }
        unset($this->internal[$offset]);
    }
}
