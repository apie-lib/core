<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\StrategyInterface;
use ReflectionClass;
use ReflectionEnum;
use ReflectionFiber;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

class BuiltInPhpClassStrategy implements StrategyInterface
{
    private const COMPOSITE_NAME_CLASSES = [
        ReflectionType::class,
        ReflectionNamedType::class,
        ReflectionUnionType::class,
        ReflectionIntersectionType::class,
        ReflectionClass::class,
        ReflectionMethod::class,
        ReflectionProperty::class,
        ReflectionParameter::class,
        ReflectionEnum::class,
        ReflectionFiber::class,
    ];

    public static function supports(ReflectionClass $class): bool
    {
        return in_array($class->name, self::COMPOSITE_NAME_CLASSES);
    }

    /**
     * @return ReflectionClass<object>
     */
    public function getClass(): ReflectionClass
    {
        return $this->class;
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata
    {
        return new CompositeMetadata(
            new MetadataFieldHashmap([])
        );
    }
    public function getModificationMetadata(ApieContext $context): CompositeMetadata
    {
        return new CompositeMetadata(
            new MetadataFieldHashmap([])
        );
    }
    public function getResultMetadata(ApieContext $context): CompositeMetadata
    {
        return new CompositeMetadata(
            new MetadataFieldHashmap([])
        );
    }
}
