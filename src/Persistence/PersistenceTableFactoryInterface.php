<?php
namespace Apie\Core\Persistence;

interface PersistenceTableFactoryInterface
{
    public function supports(PersistenceMetadataContext $context): bool;

    public function createMetadataFor(PersistenceMetadataContext $context): PersistenceTableInterface;
}
