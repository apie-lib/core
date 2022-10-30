<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Attributes\ColumnPriority;
use Apie\Core\Attributes\Context;
use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\Concerns\UseContextKey;
use ReflectionParameter;
use ReflectionType;

class ConstructorParameter implements FieldInterface
{
    use UseContextKey;

    public function __construct(private readonly ReflectionParameter $parameter)
    {
    }

    public function isRequired(): bool
    {
        return !$this->parameter->isDefaultValueAvailable();
    }

    public function isField(): bool
    {
        return !$this->parameter->getAttributes(Context::class);
    }

    public function appliesToContext(ApieContext $apieContext): bool
    {
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
