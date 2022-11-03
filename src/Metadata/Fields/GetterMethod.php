<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Attributes\ColumnPriority;
use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\Concerns\UseContextKey;
use Apie\Core\Metadata\GetterInterface;
use ReflectionMethod;
use ReflectionType;

final class GetterMethod implements FieldInterface, GetterInterface
{
    use UseContextKey;

    public function __construct(private readonly ReflectionMethod $method)
    {
    }

    public function isRequired(): bool
    {
        return true;
    }

    public function isField(): bool
    {
        return true;
    }

    public function appliesToContext(ApieContext $apieContext): bool
    {
        if (!$apieContext->appliesToContext($this->method)) {
            return false;
        }
        $parameters = $this->method->getParameters();
        foreach ($parameters as $parameter) {
            $contextKey = $this->getContextKey($apieContext, $parameter);
            if ($contextKey === null || ($parameter->isDefaultValueAvailable() && !$apieContext->hasContext($contextKey))) {
                return false;
            }
        }
        return true;
    }

    public function getValue(object $object, ApieContext $apieContext): mixed
    {
        $arguments = [];
        foreach ($this->method->getParameters() as $parameter) {
            $contextKey = $this->getContextKey($apieContext, $parameter);
            if ($contextKey === null || !$apieContext->hasContext($contextKey)) {
                $arguments[] = $parameter->getDefaultValue();
            } else {
                $arguments[] = $apieContext->getContext($contextKey);
            }
        }

        return $this->method->invokeArgs($object, $arguments);
    }

    public function getFieldPriority(): ?int
    {
        $attributes = $this->method->getAttributes(ColumnPriority::class);
        if (empty($attributes)) {
            return null;
        }

        $attribute = reset($attributes);
        return $attribute->newInstance()->priority;
    }

    public function getTypehint(): ?ReflectionType
    {
        return $this->method->getReturnType();
    }
}
