<?php
namespace Apie\Core\Lists;

use ArrayAccess;
use Countable;
use JsonSerializable;
use stdClass;

/**
 * @template T
 */
interface HashmapInterface extends ArrayAccess, JsonSerializable, Countable, Arrayable
{
    /**
     * @param int|string $offset
     * @return T
     */
    public function offsetGet(mixed $offset): mixed;
    /**
     * @param int|string $offset
     * @param T $value
     */
    public function offsetSet(mixed $offset, mixed $value): void;

    public function jsonSerialize(): stdClass;
}
