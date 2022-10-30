<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Attributes\ColumnPriority;
use Apie\Core\Attributes\Optional;
use Apie\Core\Context\ApieContext;
use ReflectionProperty;
use ReflectionType;

final class PublicProperty implements FieldInterface
{
    private bool $required;

    public function __construct(private readonly ReflectionProperty $property, bool $optional = false)
    {
        $this->required = !$optional && empty($property->getAttributes(Optional::class)) && !$this->property->hasDefaultValue();
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isField(): bool
    {
        return true;
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

    public function getTypehint(): ?ReflectionType
    {
        return $this->property->getType();
    }
}
