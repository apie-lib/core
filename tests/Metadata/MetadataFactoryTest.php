<?php
namespace Apie\Tests\Core\Metadata;

use Apie\Core\ApieLib;
use Apie\Core\Attributes\Context;
use Apie\Core\Attributes\Optional;
use Apie\Core\Attributes\RuntimeCheck;
use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\Fields\ConstructorParameter;
use Apie\Core\Metadata\Fields\DiscriminatorColumn;
use Apie\Core\Metadata\Fields\FieldInterface;
use Apie\Core\Metadata\Fields\GetterMethod;
use Apie\Core\Metadata\Fields\OptionalField;
use Apie\Core\Metadata\Fields\PublicProperty;
use Apie\Core\Metadata\Fields\SetterMethod;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\Strategy\AliasStrategy;
use Apie\Core\Metadata\Strategy\BuiltInPhpClassStrategy;
use Apie\Core\Metadata\Strategy\CompositeValueObjectStrategy;
use Apie\Core\Metadata\Strategy\DtoStrategy;
use Apie\Core\Metadata\Strategy\EnumStrategy;
use Apie\Core\Metadata\Strategy\ItemHashmapStrategy;
use Apie\Core\Metadata\Strategy\ItemListObjectStrategy;
use Apie\Core\Metadata\Strategy\PolymorphicEntityStrategy;
use Apie\Core\Metadata\Strategy\RegularObjectStrategy;
use Apie\Core\Metadata\Strategy\ValueObjectStrategy;
use Apie\Core\Permissions\PermissionInterface;
use Apie\CountryAndPhoneNumber\DutchPhoneNumber;
use Apie\Fixtures\Context\IsActivatedUser;
use Apie\Fixtures\Dto\DefaultExampleDto;
use Apie\Fixtures\Dto\EmptyDto;
use Apie\Fixtures\Dto\ExampleDto;
use Apie\Fixtures\Dto\OptionalExampleDto;
use Apie\Fixtures\Entities\CollectionItemOwned;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\Polymorphic\Animal;
use Apie\Fixtures\Entities\Polymorphic\Cow;
use Apie\Fixtures\Entities\Polymorphic\MixedTypes;
use Apie\Fixtures\Enums\EmptyEnum;
use Apie\Fixtures\FuturePhpVersion;
use Apie\Fixtures\Identifiers\UserAutoincrementIdentifier;
use Apie\Fixtures\Lists\ImmutableStringOrIntHashmap;
use Apie\Fixtures\Lists\ImmutableStringOrIntList;
use Apie\Fixtures\Php84\AsyncVisibility;
use Apie\Fixtures\Php84\PropertyHooks;
use Apie\Fixtures\ValueObjects\CompositeValueObjectExample;
use Apie\Fixtures\ValueObjects\CompositeValueObjectWithOptionalFields;
use Apie\Fixtures\ValueObjects\Password;
use Apie\TypeConverter\ReflectionTypeFactory;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Stringable;

class MetadataFactoryTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        ApieLib::resetAliases();
        if (PHP_VERSION_ID >= 80400) {
            FuturePhpVersion::loadPhp84Classes();
        }
    }
    #[\PHPUnit\Framework\Attributes\DataProvider('provideStrategy')]
    public function testGetMetadataStrategy(string $expectedStrategyClass, string $input)
    {
        $this->assertInstanceOf($expectedStrategyClass, MetadataFactory::getMetadataStrategy(new ReflectionClass($input)));
    }

    public static function provideStrategy()
    {
        yield 'Non value object, non entity' => [
            RegularObjectStrategy::class, __CLASS__
        ];
        yield 'Entity' => [
            RegularObjectStrategy::class, Order::class,
        ];
        yield 'Polymorphic entity, base class' => [
            PolymorphicEntityStrategy::class, Animal::class,
        ];
        yield 'Polymorphic entity, child class' => [
            PolymorphicEntityStrategy::class, Cow::class,
        ];
        yield 'Regular DTO' => [
            DtoStrategy::class, DefaultExampleDto::class,
        ];
        yield 'Empty DTO' => [
            DtoStrategy::class, EmptyDto::class,
        ];
        yield 'Empty Enum' => [
            EnumStrategy::class, EmptyEnum::class,
        ];
        yield 'Item Hashmap' => [
            ItemHashmapStrategy::class, ItemHashmap::class,
        ];
        yield 'Immutable Item Hashmap' => [
            ItemHashmapStrategy::class, ImmutableStringOrIntHashmap::class,
        ];
        yield 'Item List' => [
            ItemListObjectStrategy::class, ItemList::class,
        ];
        yield 'Immutable Item List' => [
            ItemListObjectStrategy::class, ImmutableStringOrIntList::class,
        ];
        yield 'Password value object' => [
            ValueObjectStrategy::class, Password::class,
        ];
        yield 'Built in PHP Interface' => [
            BuiltInPhpClassStrategy::class, Stringable::class,
        ];
        yield 'Built in PHP Class' => [
            BuiltInPhpClassStrategy::class, ReflectionClass::class,
        ];
        yield 'Apie lib alias' => [
            AliasStrategy::class,
            PermissionInterface::class
        ];
    
        yield 'Composite value object' => [
            CompositeValueObjectStrategy::class,
            CompositeValueObjectExample::class,
        ];

        if (class_exists(DutchPhoneNumber::class)) {
            yield 'Dutch phone number' => [
                ValueObjectStrategy::class,
                DutchPhoneNumber::class
            ];
        }

        if (PHP_VERSION_ID >= 80400) {
            yield 'Object with property hooks' => [
                RegularObjectStrategy::class,
                PropertyHooks::class,
            ];
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('getScalarForTypeProvider')]
    public function testGetScalarForType(ScalarType $expected, ?string $typehint)
    {
        $type = $typehint === null ? null : ReflectionTypeFactory::createReflectionType($typehint);
        $this->assertEquals($expected, MetadataFactory::getScalarForType($type));
    }

    public static function getScalarForTypeProvider(): Generator
    {
        yield 'string typehint' => [
            ScalarType::STRING,
            'string'
        ];
        yield 'nullable string typehint' => [
            ScalarType::STRING,
            '?string'
        ];
        yield 'value object' => [
            ScalarType::INTEGER,
            UserAutoincrementIdentifier::class
        ];
        yield 'Apie lib alias' => [
            ScalarType::STRING,
            PermissionInterface::class
        ];
    }

    /**
     * @param array<int, string> $expectedFields
     * @param array<int, string> $expectedRequired
     * @param class-string<object> $className
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('compositeMetadataProvider')]
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

    #[RequiresPhp('>=8.4')]
    public function testCompositeMetadataWithPropertyHooks()
    {
        $context = new ApieContext([]);
        /** @var CompositeMetadata $actual */
        $actual = MetadataFactory::getResultMetadata(new ReflectionClass(PropertyHooks::class), $context);
        $this->assertInstanceOf(CompositeMetadata::class, $actual);
        $hashmap = $actual->getHashmap();
        $filteredHashmap = $hashmap->filterOnContext($context, true);
        $this->assertEquals(['name', 'virtual'], array_keys($filteredHashmap->toArray()));
    }

    public static function compositeMetadataProvider()
    {
        $context = new ApieContext();
        yield 'Creation of entity' => [
            ['id', 'optionalTags', 'orderLines'],
            ['id', 'orderLines'],
            'getCreationMetadata',
            Order::class,
            $context
        ];
        yield 'Modification of entity' => [
            ['optionalTags'],
            [],
            'getModificationMetadata',
            Order::class,
            $context
        ];
        yield 'Retrieve an entity' => [
            ['id', 'orderStatus', 'optionalTags', 'orderLines'],
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
        yield 'Creation of polymorphic entity, mixed fields' => [
            ['type', 'id', 'name', 'value', 'step', 'nullableValue', 'uniqueToInteger', 'uniqueToString'],
            ['type', 'id', 'name', 'value', 'step', 'nullableValue', 'uniqueToInteger', 'uniqueToString'],
            'getCreationMetadata',
            MixedTypes::class,
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
        if (PHP_VERSION_ID >= 80400) {
            yield 'Object with property hooks creation' => [
                ['name', 'virtualSetter', 'virtual'],
                ['name'],
                'getCreationMetadata',
                PropertyHooks::class,
                $context
            ];
            yield 'Object with property hooks modification' => [
                ['name', 'virtualSetter', 'virtual'],
                [],
                'getModificationMetadata',
                PropertyHooks::class,
                $context
            ];
            yield 'Object with property hooks retrieval' => [
                ['name', 'virtualSetter', 'virtual'],
                ['name', 'virtual'],
                'getResultMetadata',
                PropertyHooks::class,
                $context
            ];
            yield 'Object with async visibility creation' => [
                ['name', 'option'],
                ['name', 'option'],
                'getCreationMetadata',
                AsyncVisibility::class,
                $context
            ];
            yield 'Object with async visibility modification' => [
                [],
                [],
                'getModificationMetadata',
                AsyncVisibility::class,
                $context
            ];
            yield 'Object with async visibility retrieval' => [
                ['name', 'option'],
                ['name', 'option'],
                'getResultMetadata',
                AsyncVisibility::class,
                $context
            ];
        }
    }

    #[Test]
    #[DataProvider('php8attributeProvider')]
    public function it_can_show_php8_attributes(array $expected, string $attributeClass, FieldInterface $field, bool $classDocBlock = true, bool $propertyDocblock = true, bool $argumentDocBlock = true)
    {
        $this->assertEquals(
            $expected,
            $field->getAttributes($attributeClass, $classDocBlock, $propertyDocblock, $argumentDocBlock)
        );
    }

    public static function php8attributeProvider(): \Generator
    {
        yield 'constructor parameter' => [
            [
                new Context('authenticated')
            ],
            Context::class,
            new ConstructorParameter(
                (new ReflectionClass(CollectionItemOwned::class))->getConstructor()->getParameters()[1]
            ),
        ];
        yield 'setter method' => [
            [],
            Context::class,
            new SetterMethod(
                (new ReflectionClass(CollectionItemOwned::class))->getMethod('setOwned')
            ),
        ];
        $setterField = new SetterMethod(
            (new ReflectionClass(CollectionItemOwned::class))->getMethod('setOwned')
        );
        yield 'setter method, no argument docblock' => [
            [],
            Context::class,
            $setterField,
            true,
            true,
            false
        ];
        $getterField = new GetterMethod(
            (new ReflectionClass(CollectionItemOwned::class))->getMethod('getCreatedBy')
        );
        yield 'getter method' => [
            [
                new RuntimeCheck(new IsActivatedUser()),
            ],
            RuntimeCheck::class,
            $getterField,
        ];
        yield 'optional field 1' => [
            [
                new RuntimeCheck(new IsActivatedUser()),
            ],
            RuntimeCheck::class,
            new OptionalField(
                $setterField,
                $getterField
            )
        ];
        yield 'optional field 2' => [
            [
                new RuntimeCheck(new IsActivatedUser()),
            ],
            RuntimeCheck::class,
            new OptionalField(
                $setterField,
                $getterField
            )
        ];
        yield 'discriminator column' => [
            [],
            Context::class,
            new DiscriminatorColumn(Animal::getDiscriminatorMapping())
        ];
        yield 'public property' => [
            [new Optional()],
            Optional::class,
            new PublicProperty(new ReflectionProperty(OptionalExampleDto::class, 'optionalString'))
        ];
    }
}
