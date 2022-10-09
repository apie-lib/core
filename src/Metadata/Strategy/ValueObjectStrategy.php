<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\ScalarMetadata;
use Apie\Core\Metadata\StrategyInterface;
use Apie\Core\Metadata\ValueObjectMetadata;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;

final class ValueObjectStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return $class->implementsInterface(ValueObjectInterface::class);
    }

    public function getCreationMetadata(ApieContext $context): ValueObjectMetadata
    {
        return new ValueObjectMetadata($this->class);
    }
}