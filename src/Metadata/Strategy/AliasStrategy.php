<?php

namespace Apie\Core\Metadata\Strategy;

use Apie\Core\ApieLib;
use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\MetadataInterface;
use Apie\Core\Metadata\StrategyInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionClass;
use ReflectionType;

class AliasStrategy implements StrategyInterface
{
    private readonly ReflectionType $type;
    public function __construct(string $typehint)
    {
        $this->type = ReflectionTypeFactory::createReflectionType(ApieLib::getAlias($typehint));
    }
    public static function supports(ReflectionClass $class): bool
    {
        return ApieLib::hasAlias($class->name);
    }

    public function getCreationMetadata(ApieContext $context): MetadataInterface
    {
        return MetadataFactory::getCreationMetadata($this->type, $context);
    }

    public function getModificationMetadata(ApieContext $context): MetadataInterface
    {
        return MetadataFactory::getModificationMetadata($this->type, $context);
    }

    public function getResultMetadata(ApieContext $context): MetadataInterface
    {
        return MetadataFactory::getResultMetadata($this->type, $context);
    }
}