<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Context\ApieContext;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionType;

/**
 * DatePeriod::$options has incorrect reflection metadata, so we need to override it.
 */
class DatePeriodOptions implements FieldInterface
{
    public function getAttributes(
        string $attributeClass,
        bool $classDocBlock = true,
        bool $propertyDocblock = true,
        bool $argumentDocBlock = true
    ): array {
        return [];
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
        return true;
    }
    public function getFieldPriority(): ?int
    {
        return -1;
    }

    public function getTypehint(): ?ReflectionType
    {
        return ReflectionTypeFactory::createReflectionType('int');
    }

    public function allowsNull(): bool
    {
        return false;
    }
}
