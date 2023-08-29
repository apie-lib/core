<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\EntityInterface;

final class Indexer
{
    /**
     * @var array<string, IndexingStrategyInterface>
     */
    private array $strategies;

    public function __construct(IndexingStrategyInterface... $strategies)
    {
        $this->strategies = $strategies;
    }

    public static function create(): self
    {
        return new self(
            new SkipPasswordFields(),
            new FromAttribute(),
            new FromGetters()
        );
    }

    /**
     * @return array<string, int>
     */
    public function getIndexesForEntity(EntityInterface $entity, ApieContext $apieContext): array
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($entity)) {
                return $strategy->getIndexes($entity, $apieContext);
            }
        }
        return [];
    }
}
