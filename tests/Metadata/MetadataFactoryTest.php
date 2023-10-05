<?php
namespace Apie\Tests\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\Strategy\BuiltInPhpClassStrategy;
use Apie\Core\Metadata\Strategy\CompositeValueObjectStrategy;
use Apie\Core\Metadata\Strategy\DtoStrategy;
use Apie\Core\Metadata\Strategy\EnumStrategy;
use Apie\Core\Metadata\Strategy\ItemHashmapStrategy;
use Apie\Core\Metadata\Strategy\ItemListObjectStrategy;
use Apie\Core\Metadata\Strategy\PolymorphicEntityStrategy;
use Apie\Core\Metadata\Strategy\RegularObjectStrategy;
use Apie\Core\Metadata\Strategy\ValueObjectStrategy;
use Apie\Fixtures\Dto\DefaultExampleDto;
use Apie\Fixtures\Dto\EmptyDto;
use Apie\Fixtures\Dto\ExampleDto;
use Apie\Fixtures\Dto\OptionalExampleDto;
use Apie\Fixtures\Entities\CollectionItemOwned;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\Polymorphic\Animal;
use Apie\Fixtures\Entities\Polymorphic\Cow;
use Apie\Fixtures\Enums\EmptyEnum;
use Apie\Fixtures\Lists\ImmutableStringOrIntHashmap;
use Apie\Fixtures\Lists\ImmutableStringOrIntList;
use Apie\Fixtures\ValueObjects\CompositeValueObjectExample;
use Apie\Fixtures\ValueObjects\CompositeValueObjectWithOptionalFields;
use Apie\Fixtures\ValueObjects\Password;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Stringable;

class MetadataFactoryTest extends TestCase
{
    /**
     * @dataProvider provideStrategy
     */
    public function testGetMetadataStrategy(string $expectedStrategyClass, string $input)
    {
        $this->assertInstanceOf($expectedStrategyClass, MetadataFactory::getMetadataStrategy(new ReflectionClass($input)));
    }

    public function provideStrategy()
    {
        yield [
            RegularObjectStrategy::class, __CLASS__
        ];
        yield [
            RegularObjectStrategy::class, Order::class,
        ];
        yield [
            PolymorphicEntityStrategy::class, Animal::class,
        ];
        yield [
            PolymorphicEntityStrategy::class, Cow::class,
        ];
        yield [
            DtoStrategy::class, DefaultExampleDto::class,
        ];
        yield [
            DtoStrategy::class, EmptyDto::class,
        ];
        yield [
            EnumStrategy::class, EmptyEnum::class,
        ];
        yield [
            ItemHashmapStrategy::class, ItemHashmap::class,
        ];
        yield [
            ItemHashmapStrategy::class, ImmutableStringOrIntHashmap::class,
        ];
        yield [
            ItemListObjectStrategy::class, ItemList::class,
        ];
        yield [
            ItemListObjectStrategy::class, ImmutableStringOrIntList::class,
        ];
        yield [
            ValueObjectStrategy::class, Password::class,
        ];
        yield [
            BuiltInPhpClassStrategy::class, Stringable::class,
        ];
        yield [
            BuiltInPhpClassStrategy::class, ReflectionClass::class,
        ];
    
        yield [
            CompositeValueObjectStrategy::class,
            CompositeValueObjectExample::class,
        ];
    }

    /**
     * @param array<int, string> $expectedFields
     * @param array<int, string> $expectedRequired
     * @param class-string<object> $className
     * @dataProvider compositeMetadataProvider
     */
    public function testCompositeMetadata(
        array $expectedFields,
        array $expectedRequired,
        string $methodName,
        string $className,
        ApieContext $context
    ) {
        /** @var CompositeMetadata $actual */
        $actual = MetadataFactory::$methodName(new ReflectionClass($className), $context);
        $this->assertInstanceOf(CompositeMetadata::class, $actual);
        $this->assertEquals($expectedFields, array_keys($actual->getHashmap()->toArray()));
        $this->assertEquals($expectedRequired, $actual->getRequiredFields()->toArray());
    }

    public function testCompositeMetadataWithContext()
    {
        $context = new ApieContext([]);
        /** @var CompositeMetadata $actual */
        $actual = MetadataFactory::getResultMetadata(new ReflectionClass(CollectionItemOwned::class), $context);
        $this->assertInstanceOf(CompositeMetadata::class, $actual);
        $hashmap = $actual->getHashmap();
        $filteredHashmap = $hashmap->filterOnContext($context, true);
        $this->assertEquals(['id', 'owned'], array_keys($filteredHashmap->toArray()));
    }

    public function compositeMetadataProvider()
    {
        $context = new ApieContext();
        yield 'Creation of entity' => [
            ['id', 'orderLines'],
            ['id', 'orderLines'],
            'getCreationMetadata',
            Order::class,
            $context
        ];
        yield 'Modification of entity' => [
            [],
            [],
            'getModificationMetadata',
            Order::class,
            $context
        ];
        yield 'Retrieve an entity' => [
            ['id', 'orderStatus', 'orderLines'],
            ['id', 'orderStatus', 'orderLines'],
            'getResultMetadata',
            Order::class,
            $context
        ];
        yield 'Creation of polymorphic entity, base class' => [
            ['animalType', 'id', 'hasMilk', 'starving', 'poisonous'],
            ['animalType'],
            'getCreationMetadata',
            Animal::class,
            $context
        ];
        yield 'Modification of polymorphic entity, base class' => [
            ['hasMilk', 'starving', 'poisonous'],
            [],
            'getModificationMetadata',
            Animal::class,
            $context
        ];
        yield 'Retrieve of polymorphic entity, base class' => [
            ['animalType', 'id', 'hasMilk', 'starving', 'poisonous'],
            ['animalType', 'id'],
            'getResultMetadata',
            Animal::class,
            $context
        ];
        yield 'Creation of polymorphic entity, child class' => [
            ['animalType', 'id', 'hasMilk'],
            ['animalType'],
            'getCreationMetadata',
            Cow::class,
            $context
        ];
        yield 'Modification of polymorphic entity, child class' => [
            ['hasMilk'],
            [],
            'getModificationMetadata',
            Cow::class,
            $context
        ];
        yield 'Retrieve of polymorphic entity, child class' => [
            ['animalType', 'id', 'hasMilk'],
            ['animalType', 'id'],
            'getResultMetadata',
            Cow::class,
            $context
        ];
        yield 'Creation of DTO with default values' => [
            [
                'string',
                'integer',
                'floatingPoint',
                'trueOrFalse',
                'mixed',
                'noType',
                'gender',
            ],
            [],
            'getCreationMetadata',
            DefaultExampleDto::class,
            $context
        ];
        yield 'Modification of DTO with default values' => [
            [
                'string',
                'integer',
                'floatingPoint',
                'trueOrFalse',
                'mixed',
                'noType',
                'gender',
            ],
            [],
            'getModificationMetadata',
            DefaultExampleDto::class,
            $context
        ];
        yield 'Retrieve of DTO with default values' => [
            [
                'string',
                'integer',
                'floatingPoint',
                'trueOrFalse',
                'mixed',
                'noType',
                'gender',
            ],
            [],
            'getResultMetadata',
            DefaultExampleDto::class,
            $context
        ];
        yield 'Creation of DTO' => [
            [
                'string',
                'integer',
                'floatingPoint',
                'trueOrFalse',
                'mixed',
                'noType',
                'gender',
            ],
            [
                'string',
                'integer',
                'floatingPoint',
                'trueOrFalse',
                'mixed',
                'noType',
                'gender',
            ],
            'getCreationMetadata',
            ExampleDto::class,
            $context
        ];
        yield 'Modification of DTO' => [
            [
                'string',
                'integer',
                'floatingPoint',
                'trueOrFalse',
                'mixed',
                'noType',
                'gender',
            ],
            [
            ],
            'getModificationMetadata',
            ExampleDto::class,
            $context
        ];
        yield 'Retrieve of DTO' => [
            [
                'string',
                'integer',
                'floatingPoint',
                'trueOrFalse',
                'mixed',
                'noType',
                'gender',
            ],
            [
                'string',
                'integer',
                'floatingPoint',
                'trueOrFalse',
                'mixed',
                'noType',
                'gender',
            ],
            'getResultMetadata',
            ExampleDto::class,
            $context
        ];
        yield 'Creation of DTO with @Optional attribute' => [
            [
                'optionalString',
                'optionalInteger',
                'optionalFloatingPoint',
                'optionalTrueOrFalse',
                'mixed',
                'noType',
                'optionalGender',
            ],
            [],
            'getCreationMetadata',
            OptionalExampleDto::class,
            $context
        ];
        yield 'Retrieve of DTO with @Optional attribute' => [
            [
                'optionalString',
                'optionalInteger',
                'optionalFloatingPoint',
                'optionalTrueOrFalse',
                'mixed',
                'noType',
                'optionalGender',
            ],
            [],
            'getResultMetadata',
            OptionalExampleDto::class,
            $context
        ];
        yield 'Creation of entity with context' => [
            ['id', 'owned', 'createdBy'],
            ['id', 'owned'],
            'getCreationMetadata',
            CollectionItemOwned::class,
            $context
        ];
        yield 'Modification of entity with context' => [
            ['owned'],
            [],
            'getModificationMetadata',
            CollectionItemOwned::class,
            $context
        ];
        yield 'Retrieve of entity with context' => [
            ['id', 'owned', 'createdBy'],
            ['id', 'owned', 'createdBy'],
            'getResultMetadata',
            CollectionItemOwned::class,
            $context
        ];

        yield 'Composite value object creation' => [
            ['withDefaultValue', 'withOptionalAttribute'],
            [],
            'getCreationMetadata',
            CompositeValueObjectWithOptionalFields::class,
            $context
        ];
        yield 'Composite value object modification' => [
            ['withDefaultValue', 'withOptionalAttribute'],
            [],
            'getModificationMetadata',
            CompositeValueObjectWithOptionalFields::class,
            $context
        ];
        yield 'Composite value object retrieval' => [
            ['withDefaultValue', 'withOptionalAttribute'],
            [],
            'getResultMetadata',
            CompositeValueObjectWithOptionalFields::class,
            $context
        ];
    }
}
