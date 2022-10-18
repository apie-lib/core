<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Lists\StringList;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\MetadataInterface;
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
     * @param ReflectionClass<object> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    public function getCreationMetadata(ApieContext $context): MetadataInterface
    {
        return new CompositeMetadata(
            new ReflectionHashmap(['name' => new ReflectionProperty($this->class, 'name')]),
            new StringList(['name'])
        );
    }
    public function getModificationMetadata(ApieContext $context): MetadataInterface
    {
        return new CompositeMetadata(
            new ReflectionHashmap([]),
            new StringList([])
        );
    }
}
