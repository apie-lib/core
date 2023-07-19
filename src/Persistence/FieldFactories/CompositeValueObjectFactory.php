<?php
namespace Apie\Core\Persistence\FieldFactories;

use Apie\Core\Persistence\Fields\FieldReference;
use Apie\Core\Persistence\Fields\IgnoredField;
use Apie\Core\Persistence\PersistenceFieldFactoryInterface;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\Core\Persistence\PersistenceMetadataContext;
use Apie\Core\ValueObjects\CompositeValueObject;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use LogicException;
use ReflectionClass;
use ReflectionProperty;

final class CompositeValueObjectFactory implements PersistenceFieldFactoryInterface
{
    public function supports(PersistenceMetadataContext $context): bool
    {
        $class = $context->getCurrentPropertyClass();
        if ($class instanceof ReflectionClass && $class->implementsInterface(ValueObjectInterface::class)) {
            return in_array(CompositeValueObject::class, $class->getTraitNames());
        }

        return false;
    }

    public function createMetadataFor(PersistenceMetadataContext $context): PersistenceFieldInterface
    {
        $property = $context->getCurrentProperty();
        assert($property instanceof ReflectionProperty);
        $ignoredField = new IgnoredField($property->getDeclaringClass()->name, $property->name);
        $context = $context->addFieldInvariant($ignoredField)->visitClass();
        $tableDefinition = $context->createTableDefinition();
        foreach ($tableDefinition->getFields() as $field) {
            if ($field->getName() === 'id') {
                return new FieldReference(
                    $property->getDeclaringClass()->name,
                    $property->name,
                    $field,
                    $tableDefinition->getName()
                );
            }
        }

        throw new LogicException('I created a table definition, but it has no id?');
    }
}
