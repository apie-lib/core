<?php
namespace Apie\Core\Context;

use Apie\Core\Attributes\AllApplies;
use Apie\Core\Attributes\AnyApplies;
use Apie\Core\Attributes\ApieContextAttribute;
use Apie\Core\Attributes\CustomContextCheck;
use Apie\Core\Attributes\Not;
use Apie\Core\Attributes\Requires;
use Apie\Core\Exceptions\IndexNotFoundException;
use ReflectionEnumUnitCase;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;

final class ApieContext
{
    /** @var array<int, class-string<ApieContextAttribute>> */
    private const ATTRIBUTES = [Requires::class, CustomContextCheck::class, AllApplies::class, AnyApplies::class,Equals::class, Not::class];

    public function __construct(private array $context = [])
    {
    }

    public function withContext(string $key, mixed $value): self
    {
        $instance = clone $this;
        $instance->context[$key] = $value;
        return $instance;
    }

    public function hasContext(string $key): bool
    {
        return array_key_exists($key, $this->context);
    }

    public function getContext(string $key): mixed
    {
        if (!array_key_exists($key, $this->context)) {
            throw new IndexNotFoundException($key);
        }

        return $this->context[$key];
    }

    public function isFiltered(ReflectionMethod|ReflectionProperty|ReflectionType|ReflectionEnumUnitCase $method): bool
    {
        foreach (self::ATTRIBUTES as $attribute) {
            foreach ($method->getAttributes($attribute) as $attribute) {
                if (!$attribute->newInstance()->applies($this)) {
                    return false;
                }
            }
        }
        return true;
    }
}
