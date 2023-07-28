<?php
namespace Apie\Core\Persistence;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Persistence\FieldFactories\AutoincrementIntegerFactory;
use Apie\Core\Persistence\FieldFactories\CompositeValueObjectFactory;
use Apie\Core\Persistence\FieldFactories\EnumFieldFactory;
use Apie\Core\Persistence\FieldFactories\ListFieldFactory;
use Apie\Core\Persistence\FieldFactories\PrimitiveFieldFactory;
use Apie\Core\Persistence\FieldFactories\PrimitiveValueObjectFactory;
use Apie\Core\Persistence\Fields\AutoincrementInteger;
use Apie\Core\Persistence\Fields\EntityGetIdValue;
use Apie\Core\Persistence\FormFactories\AutoincrementIntegerTableFactory;
use Apie\Core\Persistence\Lists\PersistenceFieldFactoryList;
use Apie\Core\Persistence\Lists\PersistenceFieldList;
use Apie\Core\Persistence\Lists\PersistenceTableFactoryList;
use Apie\Core\Persistence\Metadata\EntityInvariantMetadata;
use Apie\Core\Persistence\Metadata\EntityMetadata;
use Apie\DoctrineEntityConverter\PropertyGenerators\AutoincrementIntegerPropertyGenerator;
use LogicException;
use ReflectionClass;

final class PersistenceMetadataFactory implements PersistenceMetadataFactoryInterface
{
    public function __construct(
        private readonly PersistenceFieldFactoryList $fieldFactories,
        private readonly PersistenceTableFactoryList $tableFactories
    ) {
    }

    public static function create(): self
    {
        return new self(
            new PersistenceFieldFactoryList([
                new AutoincrementIntegerFactory(),
                new PrimitiveFieldFactory(),
                new PrimitiveValueObjectFactory(),
                new CompositeValueObjectFactory(),
                new EnumFieldFactory(),
                new ListFieldFactory(),
            ]),
            new PersistenceTableFactoryList([
                new AutoincrementIntegerTableFactory(),
            ])
        );
    }

    public function createRelatedTable(PersistenceMetadataContext $context): PersistenceTableInterface
    {
        foreach ($this->tableFactories as $tableFactory) {
            if ($tableFactory->supports($context)) {
                return $tableFactory->createMetadataFor($context);
            }
        }

        $identifier = $context->getCurrentIdentifier() ?? new AutoincrementInteger();
        $fields = array_merge([$identifier], $context->getInvariantFields()->toArray());
        foreach ($context->getProperties($context->getCurrentObject()) as $propertyContext) {
            $field = $propertyContext->createPropertyDefinition();
            if ($field) {
                $fields[] = $field;
            }
        }

        $boundedContext = $context->getCurrentBoundedContext();
        $currentObject = $context->getOriginalObject() ?? $context->getCurrentObject();
        if (!$boundedContext || !$currentObject) {
            throw new LogicException('I have no bounded context/object and no table factory that can create a related table');
        }

        return new EntityInvariantMetadata(
            $boundedContext->getId(),
            $currentObject->name,
            $context->getInvariantPrefix(),
            new PersistenceFieldList($fields)
        );
    }

    public function createEntityMetadata(
        ReflectionClass $entity,
        BoundedContext $boundedContext,
        ?PersistenceMetadataContext $context = null
    ): PersistenceTableInterface {
        $idField = new EntityGetIdValue($entity->name);
        if ($context) {
            $context->verify($this);
        } else {
            $context = new PersistenceMetadataContext(
                $this
            );
        }
        $context = $context->useContext(
            $context->getCurrentObject(),
            $idField,
            $boundedContext
        );
        $fields = [$idField];
        foreach ($context->getProperties($entity) as $propertyContext) {
            $field = $this->createProperty($propertyContext);
            if ($field) {
                $fields[] = $field;
            }
        }

        return new EntityMetadata(
            $boundedContext->getId(),
            $entity->name,
            new PersistenceFieldList($fields)
        );
    }

    public function createProperty(PersistenceMetadataContext $context): ?PersistenceFieldInterface
    {
        foreach ($this->fieldFactories as $fieldFactory) {
            if ($fieldFactory->supports($context)) {
                return $fieldFactory->createMetadataFor($context);
            }
        }
        var_dump((string) $context->getCurrentPropertyType());

        return null;
    }
}
