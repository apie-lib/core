<?php
namespace Apie\Core\Lists;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * @template T
 */
interface ItemListInterface extends ArrayAccess, JsonSerializable, Countable, IteratorAggregate, Arrayable
{
    /**
     * @param int $offset
     * @return T
     */
    public function offsetGet(mixed $offset): mixed;
    /**
     * @param int $offset
     * @param T $value
     */
    public function offsetSet(mixed $offset, mixed $value): void;

    public function jsonSerialize(): array;
}
