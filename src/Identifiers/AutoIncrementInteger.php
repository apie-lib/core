<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Attributes\FakeMethod;
use Apie\Core\Attributes\SchemaMethod;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\Utils;
use Faker\Generator;

/**
 * Indicate an auto-increment integer.
 */
#[FakeMethod("createRandom")]
#[SchemaMethod("getSchema")]
class AutoIncrementInteger implements ValueObjectInterface
{
    /**
     * @var array<string,int>
     */
    private static array $fakeCounter = [];

    /**
     * @var array<string, string>
     */
    private static array $hash = [];

    private ?int $internal;

    final public function __construct(?int $input)
    {
        $this->internal = $input;
    }

    final public static function fromNative(mixed $input): self
    {
        return new static($input === null ? null : Utils::toInt($input));
    }
    final public function toNative(): int|null
    {
        return $this->internal;
    }

    /**
     * @return array<string, string|int>
     */
    final public static function getSchema(): array
    {
        return [
            'type' => 'integer',
            'min' => 1,
        ];
    }

    final public static function createRandom(Generator $generator): static
    {
        $hash = spl_object_hash($generator);
        if ((self::$hash[static::class] ?? '') !== $hash) {
            self::$fakeCounter[static::class] = 1;
            self::$hash[static::class] = $hash;
        }
        return new static(self::$fakeCounter[static::class]++);
    }
}
