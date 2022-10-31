<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\ScalarMetadata;
use Apie\Core\Metadata\StrategyInterface;
use ReflectionClass;
use stdClass;

final class ScalarStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return $class->name === stdClass::class;
    }

    public function __construct(private readonly ScalarType $scalarType)
    {
    }

    public function getCreationMetadata(ApieContext $context): ScalarMetadata
    {
        return new ScalarMetadata($this->scalarType);
    }

    public function getModificationMetadata(ApieContext $context): ScalarMetadata
    {
        return new ScalarMetadata($this->scalarType);
    }

    public function getResultMetadata(ApieContext $context): ScalarMetadata
    {
        return new ScalarMetadata($this->scalarType);
    }
}
