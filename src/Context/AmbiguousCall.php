<?php
namespace Apie\Core\Context;

use Apie\Core\Exceptions\AmbiguousCallException;
use JsonSerializable;
use Serializable;
use Stringable;

final class AmbiguousCall implements Serializable, JsonSerializable, Stringable
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

    public function serialize(): never
    {
        $this->throwError();
    }

    public function unserialize(string $data): never
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

    public function __call(string $method, array $args): never
    {
        $this->throwError();
    }

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