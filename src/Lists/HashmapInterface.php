<?php
namespace Apie\Core\Lists;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use stdClass;

/**
 * @template T
 * @extends ArrayAccess<string, T>
 * @extends IteratorAggregate<string, T>
 */
interface HashmapInterface extends ArrayAccess, JsonSerializable, Countable, Arrayable, IteratorAggregate
{
    /**
     * @param string $offset
     * @return T
     */
    public function offsetGet(mixed $offset): mixed;
    /**
     * @param int|string $offset
     * @param T $value
     */
    public function offsetSet(mixed $offset, mixed $value): void;

    /**
     * @return stdClass
     */
    public function jsonSerialize(): stdClass;
}
