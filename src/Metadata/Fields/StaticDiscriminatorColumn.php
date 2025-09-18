<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\PolymorphicEntityInterface;
use Apie\Core\Metadata\GetterInterface;
use Apie\Core\Other\DiscriminatorMapping;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionClass;
use ReflectionType;

class StaticDiscriminatorColumn implements FieldInterface, GetterInterface
{
    public function __construct(private string $value)
    {
    }

    public function getValue(object $object, ApieContext $apieContext): mixed
    {
        return $this->value;
    }

    /**
     * @param ReflectionClass<PolymorphicEntityInterface> $class
     */
    public function getValueForClass(ReflectionClass $class): string
    {
        return $this->value;
    }

    /** @return array<string, string> */
    public function getOptions(ApieContext $apieContext, bool $runtimeFilter = false): array
    {
        return [
            $this->value => $this->value,
        ];
    }

    public function allowsNull(): bool
    {
        return false;
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
        return true;
    }

    public function getTypehint(): ?ReflectionType
    {
        return ReflectionTypeFactory::createReflectionType('string');
    }

    public function getFieldPriority(): int
    {
        return -280;
    }

    public function getAttributes(string $attributeClass, bool $classDocBlock = true, bool $propertyDocblock = true, bool $argumentDocBlock = true): array
    {
        return [];
    }
}
