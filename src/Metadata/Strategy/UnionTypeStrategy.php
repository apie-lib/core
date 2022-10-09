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
     * @var MetadataInterface[] $metadata
     */
    private array $metadata;

    public function __construct(MetadataInterface... $metadata)
    {
        $this->metadata = $metadata;
    }

    public static function supports(ReflectionClass $class): bool
    {
        return false;
    }

    public function getCreationMetadata(ApieContext $context): UnionTypeMetadata
    {
        return new UnionTypeMetadata(...$this->metadata);
    }
}