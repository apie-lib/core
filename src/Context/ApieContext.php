<?php
namespace Apie\Core\Context;

use Apie\Core\Attributes\AllApplies;
use Apie\Core\Attributes\AnyApplies;
use Apie\Core\Attributes\ApieContextAttribute;
use Apie\Core\Attributes\CustomContextCheck;
use Apie\Core\Attributes\Not;
use Apie\Core\Attributes\Requires;
use Apie\Core\Exceptions\IndexNotFoundException;
use ReflectionClass;
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

    private function registerOrMarkAmbiguous(string $offset, object $instance): void
    {
        if (!isset($this->context[$offset])) {
            $this->context[$offset] = $instance;
            return;
        }
        if ($this->context[$offset] instanceof AmbiguousCall) {
            $this->context[$offset] = $this->context[$offset]->withAddedName(get_class($instance));
        } else {
            $this->context[$offset] = new AmbiguousCall($offset, get_class($this->context[$offset]), get_class($instance));
        }
    }

    public function registerInstance(object $object): self
    {
        $refl = new ReflectionClass($object);

        $instance = $this->withContext($refl->name, $object);
        foreach ($refl->getInterfaceNames() as $interface) {
            $instance->registerOrMarkAmbiguous($interface, $object);
        }
        $refl = $refl->getParentClass();
        while ($refl) {
            $instance->registerOrMarkAmbiguous($refl->name, $object);
            $refl = $refl->getParentClass();
        }

        return $instance;
    }

    public function getApplicableGetters(ReflectionClass $class): ReflectionHashmap
    {
        $list = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($this->appliesToContext($property)) {
                $list[$property->getName()] = $property;
            }
        }
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (preg_match('/^(get|has|is).+$/i', $method->name) && $this->appliesToContext($method)) {
                if (strpos('is', $method->name) === 0) {
                    $list[lcfirst(substr($method->name, 4))] = $method;
                } else {
                    $list[lcfirst(substr($method->name, 3))] = $method;
                }
            }
        }
        return new ReflectionHashmap($list);
    }

    public function appliesToContext(ReflectionMethod|ReflectionProperty|ReflectionType|ReflectionEnumUnitCase $method): bool
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
