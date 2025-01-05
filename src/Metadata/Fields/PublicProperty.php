<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Attributes\ColumnPriority;
use Apie\Core\Attributes\Optional;
use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\DoNotChangeUploadedFile;
use Apie\Core\Metadata\GetterInterface;
use Apie\Core\Metadata\SetterInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionProperty;
use ReflectionType;

final class PublicProperty implements FieldWithPossibleDefaultValue, GetterInterface, SetterInterface
{
    private bool $required;

    private bool $field;

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

    public function hasDefaultValue(): bool
    {
        if (null === $this->property->getType()) {
            return $this->property->getDefaultValue() !== null;
        }

        return $this->property->hasDefaultValue();
    }

    public function getDefaultValue(): mixed
    {
        return $this->property->getDefaultValue();
    }

    public function allowsNull(): bool
    {
        $type = $this->property->getType();
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
        if ($value !== DoNotChangeUploadedFile::DoNotChange) {
            $this->property->setValue($object, $value);
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
}
