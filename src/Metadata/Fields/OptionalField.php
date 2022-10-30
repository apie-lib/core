<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Context\ApieContext;
use ReflectionType;

class OptionalField implements FieldInterface
{
    public function __construct(private FieldInterface $field1, private ?FieldInterface $field2 = null)
    {
    }

    public function isRequired(): bool
    {
        if ($this->field2 !== null && !$this->field2->isRequired()) {
            return false;
        }

        return $this->field1->isRequired();
    }

    public function isField(): bool
    {
        if ($this->field2 !== null && !$this->field2->isField()) {
            return false;
        }
        return $this->field1->isField();
    }

    public function appliesToContext(ApieContext $apieContext): bool
    {
        return $this->field1->appliesToContext($apieContext);
    }

    public function getFieldPriority(): ?int
    {
        return min($this->field1->getFieldPriority(), $this->field2->getFieldPriority());
    }

    public function getTypehint(): ?ReflectionType
    {
        // TODO: merge with $this->field2
        return $this->field1->getTypehint();
    }
}
