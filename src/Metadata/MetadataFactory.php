<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\Metadata\Strategy\BuiltInPhpClassStrategy;
use Apie\Core\Metadata\Strategy\CompositeValueObjectStrategy;
use Apie\Core\Metadata\Strategy\DtoStrategy;
use Apie\Core\Metadata\Strategy\EnumStrategy;
use Apie\Core\Metadata\Strategy\ExceptionStrategy;
use Apie\Core\Metadata\Strategy\ItemHashmapStrategy;
use Apie\Core\Metadata\Strategy\ItemListObjectStrategy;
use Apie\Core\Metadata\Strategy\PolymorphicEntityStrategy;
use Apie\Core\Metadata\Strategy\RegularObjectStrategy;
use Apie\Core\Metadata\Strategy\ScalarStrategy;
use Apie\Core\Metadata\Strategy\UnionTypeStrategy;
use Apie\Core\Metadata\Strategy\ValueObjectStrategy;
use LogicException;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

final class MetadataFactory
{
    private function __construct()
    {
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public static function getMetadataStrategy(ReflectionClass $class): StrategyInterface
    {
        if (BuiltInPhpClassStrategy::supports($class)) {
            return new BuiltInPhpClassStrategy($class);
        }
        if (ScalarStrategy::supports($class)) {
            return new ScalarStrategy(ScalarType::STDCLASS);
        }
        if (EnumStrategy::supports($class)) {
            return new EnumStrategy($class);
        }
        if (PolymorphicEntityStrategy::supports($class)) {
            return new PolymorphicEntityStrategy($class);
        }
        if (CompositeValueObjectStrategy::supports($class)) {
            return new CompositeValueObjectStrategy($class);
        }
        if (ItemListObjectStrategy::supports($class)) {
            return new ItemListObjectStrategy($class);
        }
        if (ItemHashmapStrategy::supports($class)) {
            return new ItemHashmapStrategy($class);
        }
        if (DtoStrategy::supports($class)) {
            return new DtoStrategy($class);
        }
        if (ValueObjectStrategy::supports($class)) {
            return new ValueObjectStrategy($class);
        }
        if (ExceptionStrategy::supports($class)) {
            return new ExceptionStrategy($class);
        }
        if (RegularObjectStrategy::supports($class)) {
            return new RegularObjectStrategy($class);
        }

        throw new InvalidTypeException($class->name, 'Apie');
    }

    public static function getMetadataStrategyForType(ReflectionType $typehint): StrategyInterface
    {
        if ($typehint instanceof ReflectionUnionType) {
            $metadata = [];
            foreach ($typehint->getTypes() as $type) {
                $metadata[] = self::getMetadataStrategyForType($type)->getCreationMetadata(new ApieContext());
            }
            return new UnionTypeStrategy(...$metadata);
        }
        if ($typehint instanceof ReflectionIntersectionType) {
            throw new LogicException('Intersection typehints are not supported yet');
        }
        assert($typehint instanceof ReflectionNamedType);
        if ($typehint->isBuiltin()) {
            if ($typehint->getName() === 'null') {
                return new ScalarStrategy(ScalarType::NULL);
            }
            if ($typehint->getName() === 'mixed') {
                return new ScalarStrategy(ScalarType::MIXED);
            }
            $strategy = new ScalarStrategy(
                match ($typehint->getName()) {
                    'string' => ScalarType::STRING,
                    'float' => ScalarType::FLOAT,
                    'int' => ScalarType::INTEGER,
                    'array' => ScalarType::ARRAY,
                    'mixed' => ScalarType::MIXED,
                    'bool' => ScalarType::BOOLEAN,
                    'true' => ScalarType::BOOLEAN,
                    'false' => ScalarType::BOOLEAN,
                    default => throw new InvalidTypeException($typehint->getName(), 'string|float|int|null|array|mixed|bool')
                }
            );
        } else {
            $strategy = self::getMetadataStrategy(new ReflectionClass($typehint->getName()));
        }
        if ($typehint->allowsNull()) {
            return new UnionTypeStrategy($strategy, new ScalarMetadata(ScalarType::NULL));
        }

        return $strategy;
    }

    /**
     * @param ReflectionClass<object>|ReflectionType $typehint
     */
    public static function getCreationMetadata(ReflectionClass|ReflectionType $typehint, ApieContext $context): MetadataInterface
    {
        if ($typehint instanceof ReflectionType) {
            return self::getMetadataStrategyForType($typehint)->getCreationMetadata($context);
        }
        return self::getMetadataStrategy($typehint)->getCreationMetadata($context);
    }

    /**
     * @param ReflectionClass<object>|ReflectionType $typehint
     */
    public static function getModificationMetadata(ReflectionClass|ReflectionType $typehint, ApieContext $context): MetadataInterface
    {
        if ($typehint instanceof ReflectionType) {
            return self::getMetadataStrategyForType($typehint)->getModificationMetadata($context);
        }
        return self::getMetadataStrategy($typehint)->getModificationMetadata($context);
    }

    /**
     * @param ReflectionClass<object>|ReflectionType $typehint
     */
    public static function getResultMetadata(ReflectionClass|ReflectionType $typehint, ApieContext $context): MetadataInterface
    {
        if ($typehint instanceof ReflectionType) {
            return self::getMetadataStrategyForType($typehint)->getResultMetadata($context);
        }
        return self::getMetadataStrategy($typehint)->getResultMetadata($context);
    }
}