<?php
namespace Apie\Tests\Core\Metadata;

use Apie\CompositeValueObjects\CompositeValueObject;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
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
}
