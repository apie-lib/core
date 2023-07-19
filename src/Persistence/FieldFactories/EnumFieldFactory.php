<?php
namespace Apie\Core\Persistence\FieldFactories;

use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\Strategy\EnumStrategy;
use Apie\Core\Persistence\Fields\PropertyEnum;
use Apie\Core\Persistence\PersistenceFieldFactoryInterface;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\Core\Persistence\PersistenceMetadataContext;
use ReflectionProperty;

final class EnumFieldFactory implements PersistenceFieldFactoryInterface
{
    public function supports(PersistenceMetadataContext $context): bool
    {
        $propertyType = $context->getCurrentPropertyType();
        $strategy = MetadataFactory::getMetadataStrategyForType($propertyType);
        return $strategy instanceof EnumStrategy;
    }

    public function createMetadataFor(PersistenceMetadataContext $context): PersistenceFieldInterface
    {
        $property = $context->getCurrentProperty();
        assert($property instanceof ReflectionProperty);
        return new PropertyEnum($property->getDeclaringClass()->name, $property->name);
    }
}
