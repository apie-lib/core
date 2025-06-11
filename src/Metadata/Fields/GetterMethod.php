<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Attributes\ColumnPriority;
use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\Concerns\UseContextKey;
use Apie\Core\Metadata\GetterInterface;
use Apie\Core\Utils\ConverterUtils;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectException;
use ReflectionMethod;
use ReflectionType;

final class GetterMethod implements FieldInterface, GetterInterface
{
    use UseContextKey;

    public function __construct(private readonly ReflectionMethod $method)
    {
    }

    public function getReflectionMethod(): ReflectionMethod
    {
        return $this->method;
    }

    public function allowsNull(): bool
    {
        $type = $this->method->getReturnType();
        return $type === null || $type->allowsNull();
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

    public function getAttributes(string $attributeClass, bool $classDocBlock = true, bool $propertyDocblock = true, bool $argumentDocBlock = true): array
    {
        $list = [];
        if ($propertyDocblock) {
            foreach ($this->method->getAttributes($attributeClass, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $list[] = $attribute->newInstance();
            }
        }
        try {
            $class = ConverterUtils::toReflectionClass($this->method);
        } catch (CanNotConvertObjectException) {
            return $list;
        }
        if ($class && $classDocBlock) {
            foreach ($class->getAttributes($attributeClass, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $list[] = $attribute->newInstance();
            }
        }

        return $list;
    }
}
