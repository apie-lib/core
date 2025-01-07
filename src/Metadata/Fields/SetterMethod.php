<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Attributes\ColumnPriority;
use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\DoNotChangeUploadedFile;
use Apie\Core\Metadata\Concerns\UseContextKey;
use Apie\Core\Metadata\SetterInterface;
use Apie\Core\Utils\ConverterUtils;
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

    public function allowsNull(): bool
    {
        $parameters = $this->method->getParameters();
        $parameter = array_pop($parameters);
        $type = $parameter->getType();
        return $type === null || $type->allowsNull();
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
        if ($value === DoNotChangeUploadedFile::DoNotChange) {
            return;
        }
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

    public function getAttributes(string $attributeClass, bool $classDocBlock = true, bool $propertyDocblock = true, bool $argumentDocBlock = true): array
    {
        $list = [];
        if ($argumentDocBlock) {
            $arguments = $this->method->getParameters();
            $argument = end($arguments);
            if ($argument) {
                foreach ($argument->getAttributes($attributeClass) as $attribute) {
                    $list[] = $attribute->newInstance();
                }
            }
        }
        if ($propertyDocblock) {
            foreach ($this->method->getAttributes($attributeClass) as $attribute) {
                $list[] = $attribute->newInstance();
            }
        }
        $class = ConverterUtils::toReflectionClass($this->method);
        if ($class && $classDocBlock) {
            foreach ($class->getAttributes($attributeClass) as $attribute) {
                $list[] = $attribute->newInstance();
            }
        }

        return $list;
    }
}
