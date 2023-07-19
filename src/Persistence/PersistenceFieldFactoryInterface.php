<?php
namespace Apie\Core\Persistence;

interface PersistenceFieldFactoryInterface
{
    public function supports(PersistenceMetadataContext $context): bool;

    public function createMetadataFor(PersistenceMetadataContext $context): PersistenceFieldInterface;
}
