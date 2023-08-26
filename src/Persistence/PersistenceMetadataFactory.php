<?php
namespace Apie\Core\Persistence;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Persistence\FieldFactories\AutoincrementIntegerFactory;
use Apie\Core\Persistence\FieldFactories\CompositeValueObjectFactory;
use Apie\Core\Persistence\FieldFactories\DataTransferObjectFieldFactory;
use Apie\Core\Persistence\FieldFactories\EnumFieldFactory;
use Apie\Core\Persistence\FieldFactories\ListFieldFactory;
use Apie\Core\Persistence\FieldFactories\OneOnOneFieldFactory;
use Apie\Core\Persistence\FieldFactories\PrimitiveFieldFactory;
use Apie\Core\Persistence\FieldFactories\PrimitiveValueObjectFactory;
use Apie\Core\Persistence\Fields\AutoincrementInteger;
use Apie\Core\Persistence\Fields\EntityGetIdValue;
use Apie\Core\Persistence\Lists\PersistenceFieldFactoryList;
use Apie\Core\Persistence\Lists\PersistenceFieldList;
use Apie\Core\Persistence\Lists\PersistenceTableFactoryList;
use Apie\Core\Persistence\Metadata\EntityInvariantMetadata;
use Apie\Core\Persistence\Metadata\EntityMetadata;
use Apie\Core\Persistence\TableFactories\AutoincrementIntegerTableFactory;
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
                new OneOnOneFieldFactory(),
                new EnumFieldFactory(),
                new ListFieldFactory(),
            ]),
            new PersistenceTableFactoryList([
                new AutoincrementIntegerTableFactory(),
            ])
        );
    }

    public function createInvariantTable(
        ReflectionClass $class,
        PersistenceMetadataContext $context
    ): PersistenceTableInterface {
        $context->verify($this);
        foreach ($this->tableFactories as $tableFactory) {
            if ($tableFactory->supports($context)) {
                return $tableFactory->createMetadataFor($context);
            }
        }
        $boundedContext = $context->getCurrentBoundedContext();
        assert(null !== $boundedContext);
        $idField = new AutoincrementInteger();
        if ($class->implementsInterface(EntityInterface::class)) {
            $idField = new EntityGetIdValue($class->name);
        }
        $context = $context->useContext(
            $class,
            $idField,
            $boundedContext
        );
        $fields = [$idField];
        foreach ($context->getProperties($class) as $propertyContext) {
            $field = $this->createProperty($propertyContext);
            if ($field) {
                $fields[] = $field;
            }
        }
        return new EntityInvariantMetadata(
            $boundedContext->getId(),
            $class->name,
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

        return null;
    }
}
