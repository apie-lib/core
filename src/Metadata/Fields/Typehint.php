<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Context\ApieContext;
use ReflectionType;

final class Typehint implements FieldInterface
{
    public function __construct(private readonly ReflectionType $type, private bool $required)
    {
    }

    public function allowsNull(): bool
    {
        return $this->type->allowsNull();
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
        return true;
    }

    public function getFieldPriority(): ?int
    {
        return null;
    }

    public function getTypehint(): ?ReflectionType
    {
        return $this->type;
    }
}
