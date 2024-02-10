<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\CountWords\WordCounter;

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
    public function getIndexesFor(mixed $instance, ApieContext $apieContext): array
    {
        if (is_object($instance)) {
            return $this->getIndexesForObject($instance, $apieContext);
        }
        if (is_array($instance)) {
            $result = [];
            foreach ($instance as $key => $item) {
                $result[$key] = ($result[$key] ?? 0) + 1;
                $objectResult = $this->getIndexesFor($item, $apieContext);
                $result = Indexer::merge($result, $objectResult);
            }
            return $result;
        }
        if (is_string($instance)) {
            return WordCounter::countFromString($instance);
        }
        return match(get_debug_type($instance)) {
            'int' => [$instance => 1],
            'float' => [$instance => 1],
            'bool' => $instance ? ['1' => 1] : ['0' => 1],
            default => [],
        };
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
