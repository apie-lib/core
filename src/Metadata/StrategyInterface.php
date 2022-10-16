<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use ReflectionClass;

interface StrategyInterface
{
    /**
     * @param ReflectionClass<object> $class
     */
    public static function supports(ReflectionClass $class): bool;
    public function getCreationMetadata(ApieContext $context): MetadataInterface;
    public function getModificationMetadata(ApieContext $context): MetadataInterface;
}
