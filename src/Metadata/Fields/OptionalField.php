<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\GetterInterface;
use Apie\Core\Metadata\SetterInterface;
use ReflectionType;

class OptionalField implements FieldInterface, GetterInterface, SetterInterface
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

    public function setValue(object $object, mixed $value, ApieContext $apieContext): void
    {
        if ($this->field1 instanceof SetterInterface) {
            $this->field1->setValue($this->field1, $value, $apieContext);
        }
        if ($this->field2 instanceof SetterInterface) {
            $this->field2->setValue($this->field1, $value, $apieContext);
        }
    }

    public function markValueAsMissing(): void
    {
        if ($this->field1 instanceof SetterInterface) {
            $this->field1->markValueAsMissing();
        }
        if ($this->field2 instanceof SetterInterface) {
            $this->field2->markValueAsMissing();
        }
    }

    public function getValue(object $object, ApieContext $apieContext): mixed
    {
        if ($this->field1 instanceof GetterInterface) {
            return $this->field1->getValue($object, $apieContext);
        }
        if ($this->field2 instanceof GetterInterface) {
            return $this->field2->getValue($object, $apieContext);
        }

        return null;
    }

    public function getTypehint(): ?ReflectionType
    {
        // TODO: merge with $this->field2
        return $this->field1->getTypehint();
    }
}
