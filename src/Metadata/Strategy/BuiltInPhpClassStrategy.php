<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\ScalarMetadata;
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
use Stringable;

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

    private const STRING_NAME_CLASSES = [
        Stringable::class
    ];

    public static function supports(ReflectionClass $class): bool
    {
        return in_array($class->name, self::COMPOSITE_NAME_CLASSES) || in_array($class->name, self::STRING_NAME_CLASSES);
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

    private function getMetadata(): CompositeMetadata|ScalarMetadata
    {
        if (in_array($this->class->name, self::STRING_NAME_CLASSES)) {
            return new ScalarMetadata(ScalarType::STRING);
        }
        return new CompositeMetadata(
            new MetadataFieldHashmap([]),
            $this->class
        );
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata|ScalarMetadata
    {
        return $this->getMetadata();
    }
    public function getModificationMetadata(ApieContext $context): CompositeMetadata|ScalarMetadata
    {
        return $this->getMetadata();
    }
    public function getResultMetadata(ApieContext $context): CompositeMetadata|ScalarMetadata
    {
        return $this->getMetadata();
    }
}
