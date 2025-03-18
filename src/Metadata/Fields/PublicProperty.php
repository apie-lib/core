<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Attributes\ColumnPriority;
use Apie\Core\Attributes\Optional;
use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\DoNotChangeUploadedFile;
use Apie\Core\Metadata\GetterInterface;
use Apie\Core\Metadata\SetterInterface;
use Apie\Core\Utils\ConverterUtils;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionProperty;
use ReflectionType;

final class PublicProperty implements FieldWithPossibleDefaultValue, GetterInterface, SetterInterface
{
    private bool $required;

    private bool $field;

    private bool $defaultValueAvailable;
    private mixed $defaultValue;

    public function __construct(
        private readonly ReflectionProperty $property,
        bool $optional = false,
        private bool $setterHooks = false,
    ) {
        $hasDefaultValue = $this->hasDefaultValue();
        $this->field = 'never' !== (string) $this->property->getType();
        if (PHP_VERSION_ID >= 80400) {
            if ($this->setterHooks) {
                $settableType = $this->property->getSettableType();
                if ('never' === (string) $settableType) {
                    $this->field = false;
                }
            } elseif (null === $this->property->getHook(\PropertyHookType::Get)
                && null !== $this->property->getHook(\PropertyHookType::Set)
                && $this->property->isVirtual()) {
                $this->field = false;
            }
        }

        $this->required = !$optional
            && empty($property->getAttributes(Optional::class))
            && !$hasDefaultValue
            && $this->field;
    }

    /**
     * ReflectionProperty::hasDefaultValue() returns false for promoted public properties,
     * so we look up the constructors to find ReflectionParameter and return this.
     * @param \ReflectionClass<object> $class
     */
    private function findPromotedProperty(\ReflectionClass $class): ?\ReflectionParameter
    {
        foreach ($class->getConstructor()->getParameters() as $parameter) {
            if ($parameter->isPromoted()
                && $parameter->isDefaultValueAvailable()
                && $parameter->name === $this->property->name
            ) {
                return $parameter;
            }
        }
        if (!$this->property->isPrivate()) {
            $parentClass = $class->getConstructor()->getDeclaringClass()->getParentClass();
            if ($parentClass && $parentClass->name !== $class->name) {
                return $this->findPromotedProperty($parentClass);
            }
        }
        return null;
    }

    public function hasDefaultValue(): bool
    {
        if (!isset($this->defaultValueAvailable)) {
            $this->defaultValueAvailable = $this->property->hasDefaultValue();
            // if there is no typehint hasDefaultValue() always returns true
            if (null === $this->property->getType()) {
                $this->defaultValueAvailable = $this->property->getDefaultValue() !== null;
            }
            if ($this->defaultValueAvailable) {
                $this->defaultValue = $this->property->getDefaultValue();
            } elseif ($this->setterHooks && $this->property->isPromoted()) {
                $argument = $this->findPromotedProperty($this->property->getDeclaringClass());
                if ($argument && $argument->isDefaultValueAvailable()) {
                    $this->defaultValueAvailable = true;
                    $this->defaultValue = $argument->getDefaultValue();
                }
            }

        }
        return $this->defaultValueAvailable;
    }

    public function getDefaultValue(): mixed
    {
        $this->hasDefaultValue();
        return $this->defaultValue;
    }

    public function allowsNull(): bool
    {
        $type = $this->getTypehint();
        return $type === null || $type->allowsNull();
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isField(): bool
    {
        return $this->field;
    }

    public function appliesToContext(ApieContext $apieContext): bool
    {
        return $apieContext->appliesToContext($this->property);
    }

    public function getFieldPriority(): ?int
    {
        $attributes = $this->property->getAttributes(ColumnPriority::class);
        if (empty($attributes)) {
            return null;
        }

        $attribute = reset($attributes);
        return $attribute->newInstance()->priority;
    }

    public function getValue(object $object, ApieContext $apieContext): mixed
    {
        return $this->property->getValue($object);
    }

    public function setValue(object $object, mixed $value, ApieContext $apieContext): void
    {
        if ($value !== DoNotChangeUploadedFile::DoNotChange && $this->field) {
            if (!$this->property->isInitialized($object) || !$this->property->isReadOnly()) {
                $this->property->setValue($object, $value);
            }
        }
    }

    public function markValueAsMissing(): void
    {
    }

    public function getTypehint(): ?ReflectionType
    {
        if (!$this->field) {
            return ReflectionTypeFactory::createReflectionType('never');
        }
        return $this->property->getType();
    }

    public function getAttributes(string $attributeClass, bool $classDocBlock = true, bool $propertyDocblock = true, bool $argumentDocBlock = true): array
    {
        $list = [];
        if ($propertyDocblock) {
            foreach ($this->property->getAttributes($attributeClass, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $list[] = $attribute->newInstance();
            }
        }
        $class = ConverterUtils::toReflectionClass($this->property);
        if ($class && $classDocBlock) {
            foreach ($class->getAttributes($attributeClass, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $list[] = $attribute->newInstance();
            }
        }
        return $list;
    }
}
