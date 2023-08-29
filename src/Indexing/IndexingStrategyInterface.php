<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;

interface IndexingStrategyInterface
{
    public function support(object $object): bool;

    /**
     * @return array<string, int>
     */
    public function getIndexes(object $class, ApieContext $context, Indexer $indexer): array;
}
