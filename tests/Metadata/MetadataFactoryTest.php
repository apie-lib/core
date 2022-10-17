<?php
namespace Apie\Tests\Core\Metadata;

use Apie\CompositeValueObjects\CompositeValueObject;
use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\Strategy\CompositeValueObjectStrategy;
use Apie\Core\Metadata\Strategy\DtoStrategy;
use Apie\Core\Metadata\Strategy\ItemHashmapStrategy;
use Apie\Core\Metadata\Strategy\ItemListObjectStrategy;
use Apie\Core\Metadata\Strategy\PolymorphicEntityStrategy;
use Apie\Core\Metadata\Strategy\RegularObjectStrategy;
use Apie\Core\Metadata\Strategy\ValueObjectStrategy;
use Apie\Fixtures\Dto\DefaultExampleDto;
use Apie\Fixtures\Dto\EmptyDto;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\Polymorphic\Animal;
use Apie\Fixtures\Entities\Polymorphic\Cow;
use Apie\Fixtures\Lists\ImmutableStringOrIntHashmap;
use Apie\Fixtures\Lists\ImmutableStringOrIntList;
use Apie\Fixtures\ValueObjects\CompositeValueObjectExample;
use Apie\Fixtures\ValueObjects\CompositeValueObjectWithOptionalFields;
use Apie\Fixtures\ValueObjects\Password;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

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
        if (trait_exists(CompositeValueObject::class)) {
            yield [
                CompositeValueObjectStrategy::class,
                CompositeValueObjectExample::class,
            ];
        }
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

    public function compositeMetadataProvider()
    {
        $context = new ApieContext();
        yield 'Creation of entity' => [
            ['id', 'orderLineList'],
            ['id', 'orderLineList'],
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
        yield 'Creation of polymorphic entity, base class' => [
            ['animalType', 'hasMilk', 'id', 'starving', 'poisonous'],
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
        yield 'Creation of polymorphic entity, child class' => [
            ['animalType', 'hasMilk', 'id'],
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
        if (trait_exists(CompositeValueObject::class)) {
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
        }
    }
}
