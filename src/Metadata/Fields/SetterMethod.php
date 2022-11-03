<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Attributes\ColumnPriority;
use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\Concerns\UseContextKey;
use Apie\Core\Metadata\SetterInterface;
use ReflectionMethod;
use ReflectionType;

final class SetterMethod implements FieldInterface, SetterInterface
{
    use UseContextKey;

    public function __construct(private readonly ReflectionMethod $method)
    {
    }

    public function getMethod(): ReflectionMethod
    {
        return $this->method;
    }

    public function isRequired(): bool
    {
        return false;
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
        // last argument is value set, so we skip that one.
        array_pop($parameters);
        foreach ($parameters as $parameter) {
            $contextKey = $this->getContextKey($apieContext, $parameter);
            if ($contextKey === null || ($parameter->isDefaultValueAvailable() && !$apieContext->hasContext($contextKey))) {
                return false;
            }
        }
        return true;
    }

    public function markValueAsMissing(): void
    {
    }

    public function setValue(object $object, mixed $value, ApieContext $apieContext): void
    {
        $parameters = $this->method->getParameters();
        // last argument is value set, so we skip that one.
        array_pop($parameters);
        $arguments = [];
        foreach ($parameters as $parameter) {
            $contextKey = $this->getContextKey($apieContext, $parameter);
            if ($contextKey === null || !$apieContext->hasContext($contextKey)) {
                $arguments[] = $parameter->getDefaultValue();
            } else {
                $arguments[] = $apieContext->getContext($contextKey);
            }
        }
        $arguments[] = $value;

        $this->method->invokeArgs($object, $arguments);
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
        $parameters = $this->method->getParameters();
        // last argument is value set, so also the typehint
        $parameter = array_pop($parameters);
        return $parameter->getType();
    }
}
