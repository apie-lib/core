<?php
namespace Apie\Core\Persistence\FieldFactories;

use Apie\Core\Persistence\Fields\PropertyPrimitive;
use Apie\Core\Persistence\PersistenceFieldFactoryInterface;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\Core\Persistence\PersistenceMetadataContext;
use ReflectionNamedType;
use ReflectionProperty;

final class PrimitiveFieldFactory implements PersistenceFieldFactoryInterface
{
    public function supports(PersistenceMetadataContext $context): bool
    {
        $propertyType = $context->getCurrentPropertyType();
        return ($propertyType instanceof ReflectionNamedType && $propertyType->isBuiltin());
    }

    public function createMetadataFor(PersistenceMetadataContext $context): PersistenceFieldInterface
    {
        $property = $context->getCurrentProperty();
        assert($property instanceof ReflectionProperty);
        return new PropertyPrimitive($property->getDeclaringClass()->name, $property->name);
    }
}
