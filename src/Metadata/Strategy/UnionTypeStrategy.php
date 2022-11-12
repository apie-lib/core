<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\MetadataInterface;
use Apie\Core\Metadata\StrategyInterface;
use Apie\Core\Metadata\UnionTypeMetadata;
use ReflectionClass;

final class UnionTypeStrategy implements StrategyInterface
{
    /**
     * @var array<int, MetadataInterface|StrategyInterface> $metadata
     */
    private array $metadata;

    public function __construct(MetadataInterface|StrategyInterface... $metadata)
    {
        $this->metadata = $metadata;
    }

    public static function supports(ReflectionClass $class): bool
    {
        return false;
    }

    /**
     * @return array<int, MetadataInterface>
     */
    private function createList(ApieContext $context, string $method): array
    {
        $list = [];
        foreach ($this->metadata as $metadata) {
            if ($metadata instanceof MetadataInterface) {
                $list[] = $metadata;
            } else {
                $list[] = $metadata->$method($context);
            }
        }

        return $list;
    }

    public function getCreationMetadata(ApieContext $context): UnionTypeMetadata
    {
        return new UnionTypeMetadata(...$this->createList($context, 'getCreationMetadata'));
    }

    public function getModificationMetadata(ApieContext $context): UnionTypeMetadata
    {
        return new UnionTypeMetadata(...$this->createList($context, 'getModificationMetadata'));
    }

    public function getResultMetadata(ApieContext $context): UnionTypeMetadata
    {
        return new UnionTypeMetadata(...$this->createList($context, 'getResultMetadata'));
    }
}
