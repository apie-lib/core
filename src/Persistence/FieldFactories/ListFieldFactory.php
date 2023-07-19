<?php
namespace Apie\Core\Persistence\FieldFactories;

use Apie\Core\Lists\ItemList;
use Apie\Core\Persistence\Fields\ListOrderNumber;
use Apie\Core\Persistence\Fields\OneToMany;
use Apie\Core\Persistence\PersistenceFieldFactoryInterface;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\Core\Persistence\PersistenceMetadataContext;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

final class ListFieldFactory implements PersistenceFieldFactoryInterface
{
    public function supports(PersistenceMetadataContext $context): bool
    {
        $class = $context->getCurrentPropertyClass();
        return ($class instanceof ReflectionClass && $class->isSubclassOf(ItemList::class));
    }

    private function fallback(PersistenceMetadataContext $context): PersistenceFieldInterface
    {
        return (new PrimitiveFieldFactory())->createMetadataFor($context);
    }

    public function createMetadataFor(PersistenceMetadataContext $context): PersistenceFieldInterface
    {
        $property = $context->getCurrentProperty();
        assert($property instanceof ReflectionProperty);
        $propertyClass = $context->getCurrentPropertyClass();
        if (!$propertyClass) {
            return $this->fallback($context);
        }
        $propertyItemType = $propertyClass->getMethod('offsetGet')->getReturnType();
        if (!$propertyItemType instanceof ReflectionNamedType || $propertyItemType->isBuiltin()) {
            return $this->fallback($context);
        }

        $context = $context->addFieldInvariant(new ListOrderNumber())
            ->useContext(
                new ReflectionClass($propertyItemType->getName()),
                $context->getCurrentIdentifier(),
                $context->getCurrentBoundedContext()
            );
        $tableDefinition = $context->createTableDefinition();

        return new OneToMany(
            $property->getDeclaringClass()->name,
            $property->name,
            $tableDefinition->getName()
        );
    }
}
