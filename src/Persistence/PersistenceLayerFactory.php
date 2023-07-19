<?php
namespace Apie\Core\Persistence;

use Apie\Core\Actions\BoundedContextEntityTuple;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\Persistence\Lists\PersistenceTableList;

final class PersistenceLayerFactory
{
    public function __construct(private readonly PersistenceMetadataFactoryInterface $factory)
    {
    }

    public function create(BoundedContextHashmap $boundedContextHashmap): PersistenceTableList
    {
        $context = new PersistenceMetadataContext($this->factory);
        foreach ($boundedContextHashmap->getTupleIterator() as $boundedContextTuple) {
            /** @var BoundedContextEntityTuple $boundedContextTuple */
            $table = $this->factory->createEntityMetadata(
                $boundedContextTuple->resourceClass,
                $boundedContextTuple->boundedContext,
                $context->useContext($boundedContextTuple->resourceClass, null, $boundedContextTuple->boundedContext)
            );
            $context->addPersistenceTable($table);
        }

        return $context->getTables();
    }
}
