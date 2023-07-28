<?php
namespace Apie\Core\Persistence\FormFactories;

use Apie\Core\Identifiers\AutoIncrementInteger;
use Apie\Core\IdentifierUtils;
use Apie\Core\Persistence\Metadata\EntityAutoincrementMetadata;
use Apie\Core\Persistence\PersistenceMetadataContext;
use Apie\Core\Persistence\PersistenceTableFactoryInterface;
use Apie\Core\Persistence\PersistenceTableInterface;

final class AutoincrementIntegerTableFactory implements PersistenceTableFactoryInterface
{
    public function supports(PersistenceMetadataContext $context): bool
    {
        $class = $context->getCurrentPropertyClass();
        return $class && ($class->name === AutoIncrementInteger::class || $class->isSubclassOf(AutoIncrementInteger::class));
    }

    public function createMetadataFor(PersistenceMetadataContext $context): PersistenceTableInterface
    {
        return new EntityAutoincrementMetadata(
            $context->getCurrentBoundedContext()->getId(),
            $context->getCurrentObject()->name,
            IdentifierUtils::propertyToUnderscore($context->getCurrentProperty())
        );
    }
}