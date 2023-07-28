<?php
namespace Apie\Core\Persistence\FieldFactories;

use Apie\Core\Identifiers\AutoIncrementInteger;
use Apie\Core\Persistence\Fields\AutoincrementIntegerReference;
use Apie\Core\Persistence\Fields\OneToMany;
use Apie\Core\Persistence\PersistenceFieldFactoryInterface;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\Core\Persistence\PersistenceMetadataContext;
use LogicException;
use ReflectionProperty;

class AutoincrementIntegerFactory implements PersistenceFieldFactoryInterface
{
    public function supports(PersistenceMetadataContext $context): bool
    {
        $class = $context->getCurrentPropertyClass();
        return $class && ($class->name === AutoIncrementInteger::class || $class->isSubclassOf(AutoIncrementInteger::class));
    }

    public function createMetadataFor(PersistenceMetadataContext $context): PersistenceFieldInterface
    {
        $property = $context->getCurrentProperty();
        assert($property instanceof ReflectionProperty);
        $tableDefinition = $context->createTableDefinition();

        return new AutoincrementIntegerReference(
            $property->getDeclaringClass()->name,
            $property->name,
            $tableDefinition->getName()
        );
    }
}