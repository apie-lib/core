<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\Core\Utils\ConverterUtils;
use ReflectionClass;
use ReflectionType;

final class EntityGetIdValue implements PersistenceFieldInterface
{
    /**
     * @param class-string<EntityInterface> $class
     */
    public function __construct(private readonly string $class)
    {
    }

    public function getName(): string
    {
        return 'id';
    }

    public function getDeclaredClass(): ?string
    {
        return null;
    }

    public function isAllowsNull(): bool
    {
        $type = $this->getType();
        if ($type->allowsNull()) {
            return true;
        }
        $class = ConverterUtils::toReflectionClass($type);
        $valueObjectType = $class->getMethod('toNative')->getReturnType();

        return $valueObjectType && $valueObjectType->allowsNull();
    }

    public function getType(): ReflectionType
    {
        return (new ReflectionClass($this->class))->getMethod('getId')->getReturnType();
    }

    public function getPersistenceType(): PersistenceColumn
    {
        $type = $this->getType();
        $class = ConverterUtils::toReflectionClass($type);

        return PersistenceColumn::createFromType($class->getMethod('toNative')->getReturnType());
    }
}
