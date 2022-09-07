<?php
namespace Apie\Core\Context;

use Apie\Core\Exceptions\AmbiguousCallException;
use JsonSerializable;
use Stringable;

/**
 * Magic null object class used by ApieContext when used with registerInstance in case an interface has already been registered.
 *
 * Any method call will throw an error.
 *
 * @see ApieContext::registerInstance()
 */
final class AmbiguousCall implements JsonSerializable, Stringable
{
    /** @var string[] */
    private array $names;
    public function __construct(private string $identifier, string... $names)
    {
        $this->names = $names;
    }

    public function withAddedName(string $name): self
    {
        $instance = clone $this;
        $instance->names[] = $name;
        return $instance;
    }

    public function __serialize(): never
    {
        $this->throwError();
    }

    /**
     * @param mixed[] $data
     */
    public function __unserialize(array $data): never
    {
        $this->throwError();
    }

    public function jsonSerialize(): never
    {
        $this->throwError();
    }

    public function __toString(): never
    {
        $this->throwError();
    }

    /**
     * @param mixed[] $args
     */
    public function __call(string $method, array $args): never
    {
        $this->throwError();
    }

    /**
     * @param mixed[] $args
     */
    public static function __callStatic(string $method, array $args): never
    {
        (new self(static::class))->throwError();
    }

    public function __set(mixed $name, mixed $value): never
    {
        $this->throwError();
    }

    public function __get(mixed $name): never
    {
        $this->throwError();
    }

    public function __invoke(): never
    {
        $this->throwError();
    }

    private function throwError(): never
    {
        throw new AmbiguousCallException($this->identifier, ...$this->names);
    }
}
