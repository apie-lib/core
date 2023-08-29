<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;

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
            new FromAttribute(),
            new SkipPasswordFields(),
            new FromItemListOrHashmap(),
            new FromValueObject(),
            new FromGetters()
        );
    }

    /**
     * @return array<string, int>
     */
    public function getIndexesForObject(object $entity, ApieContext $apieContext): array
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($entity)) {
                return $strategy->getIndexes($entity, $apieContext, $this);
            }
        }
        return [];
    }

    /**
     * @param array<string, int> $input1
     * @param array<string, int> $input2
     * @return array<string, int>
     */
    public static function merge(array $input1, array $input2): array
    {
        foreach ($input2 as $value => $prio) {
            $input1[$value] = ($input1[$value] ?? 0) + $prio;
        }

        return $input1;
    }
}
