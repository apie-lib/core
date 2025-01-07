<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Context\ApieContext;
use ReflectionType;

interface FieldInterface
{
    /**
     * @template T of object
     * @param class-string<T> $attributeClass
     * @return array<int, T>
     */
    public function getAttributes(
        string $attributeClass,
        bool $classDocBlock = true,
        bool $propertyDocblock = true,
        bool $argumentDocBlock = true
    ): array;

    public function isRequired(): bool;

    public function isField(): bool;

    public function appliesToContext(ApieContext $apieContext): bool;

    public function getFieldPriority(): ?int;

    public function getTypehint(): ?ReflectionType;

    public function allowsNull(): bool;
}
