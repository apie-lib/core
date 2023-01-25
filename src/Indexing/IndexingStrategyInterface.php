<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\EntityInterface;
use ReflectionClass;

interface IndexingStrategyInterface
{
    public function support(object $object): bool;

    /**
     * @return array<string, int>
     */
    public function getIndexes(object $class, ApieContext $context): array;
}