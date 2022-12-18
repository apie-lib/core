<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Attributes\ColumnPriority;
use Apie\Core\Attributes\Context;
use Apie\Core\Context\ApieContext;
use Apie\Core\Exceptions\IndexNotFoundException;
use Apie\Core\Metadata\Concerns\UseContextKey;
use Apie\Core\Metadata\SetterInterface;
use ReflectionParameter;
use ReflectionType;

class ConstructorParameter implements FieldWithPossibleDefaultValue, SetterInterface, FallbackFieldInterface
{
    use UseContextKey;

    public function __construct(private readonly ReflectionParameter $parameter)
    {
    }

    public function setValue(object $object, mixed $value, ApieContext $apieContext): void
    {
        // no-op
    }
    public function markValueAsMissing(): void
    {
        if (!$this->hasDefaultValue() && $this->isField()) {
            throw new IndexNotFoundException($this->parameter->name);
        }
    }

    public function getMissingValue(ApieContext $apieContext): mixed
    {
        if ($this->isField()) {
            if (!$this->hasDefaultValue()) {
                throw new IndexNotFoundException($this->parameter->name);
            }
            return $this->getDefaultValue();
        }
        $contextKey = $this->getContextKey($apieContext, $this->parameter);
        return $apieContext->getContext($contextKey);
    }

    public function allowsNull(): bool
    {
        $type = $this->parameter->getType();
        return (null === $type || $type->allowsNull());
    }

    public function hasDefaultValue(): bool
    {
        return $this->parameter->isDefaultValueAvailable();
    }

    public function getDefaultValue(): mixed
    {
        return $this->parameter->getDefaultValue();
    }

    public function isRequired(): bool
    {
        return !$this->parameter->isDefaultValueAvailable() && $this->isField();
    }

    public function isField(): bool
    {
        return !$this->parameter->getAttributes(Context::class);
    }

    public function appliesToContext(ApieContext $apieContext): bool
    {
        if ($this->isField()) {
            return true;
        }
        $contextKey = $this->getContextKey($apieContext, $this->parameter);
        return $this->parameter->isDefaultValueAvailable() || $apieContext->hasContext($contextKey);
    }

    public function getTypehint(): ?ReflectionType
    {
        return $this->parameter->getType();
    }

    public function getFieldPriority(): ?int
    {
        $attributes = $this->parameter->getAttributes(ColumnPriority::class);
        if (empty($attributes)) {
            return null;
        }

        $attribute = reset($attributes);
        return $attribute->newInstance()->priority;
    }
}
