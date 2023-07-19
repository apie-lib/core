<?php
namespace Apie\Core\Persistence\FieldFactories;

use Apie\Core\Persistence\Fields\PropertySimpleValueObject;
use Apie\Core\Persistence\PersistenceFieldFactoryInterface;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\Core\Persistence\PersistenceMetadataContext;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

final class PrimitiveValueObjectFactory implements PersistenceFieldFactoryInterface
{
    private const PRIMITIVE_VALUE_OBJECT_TYPES = [
        'string',
        'int',
        'float',
        'bool',
        'null',
    ];

    public function supports(PersistenceMetadataContext $context): bool
    {
        $class = $context->getCurrentPropertyClass();
        if ($class instanceof ReflectionClass && $class->implementsInterface(ValueObjectInterface::class)) {
            $type = $class->getMethod('toNative')->getReturnType();
            return $type instanceof ReflectionNamedType && in_array($type->getName(), self::PRIMITIVE_VALUE_OBJECT_TYPES);
        }

        return false;
    }

    public function createMetadataFor(PersistenceMetadataContext $context): PersistenceFieldInterface
    {
        $property = $context->getCurrentProperty();
        assert($property instanceof ReflectionProperty);
        return new PropertySimpleValueObject($property->getDeclaringClass()->name, $property->name);
    }
}
