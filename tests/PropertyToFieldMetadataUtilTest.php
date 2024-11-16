<?php
namespace Apie\Tests\Core;

use Apie\Core\Context\ApieContext;
use Apie\Core\FileStorage\ImageFile;
use Apie\Core\Lists\ItemList;
use Apie\Core\Metadata\Fields\ConstructorParameter;
use Apie\Core\Metadata\Fields\FieldInterface;
use Apie\Core\Metadata\Fields\PublicProperty;
use Apie\Core\Metadata\Fields\SetterMethod;
use Apie\Core\PropertyToFieldMetadataUtil;
use Apie\Core\ValueObjects\CompositeValueObject;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\OrderLine;
use Apie\Fixtures\Entities\UserWithAddress;
use Apie\Fixtures\ValueObjects\AddressWithZipcodeCheck;
use Apie\TypeConverter\ReflectionTypeFactory;
use Generator;
use LogicException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class PropertyToFieldMetadataUtilTest extends TestCase
{
    /**
     * @test
     * @dataProvider typehintProvider
     */
    public function it_can_find_typehints(bool $expected, string $input, string $searchType)
    {
        $this->assertEquals(
            $expected,
            PropertyToFieldMetadataUtil::hasPropertyWithType(
                ReflectionTypeFactory::createReflectionType($input),
                ReflectionTypeFactory::createReflectionType($searchType),
                new ApieContext()
            )
        );
    }

    public function typehintProvider(): Generator
    {
        yield [true, 'string', 'string'];
        yield [true, ImageFile::class, UploadedFileInterface::class];
        yield [true, Order::class, OrderLine::class];
    }
    /**
     * @test
     * @dataProvider fieldMetadataProvider
     */
    public function it_gets_field_metadata(?FieldInterface $expected, string $className, string $property)
    {
        $apieContext = new ApieContext();
        $class = new ReflectionClass($className);
        $this->assertEquals(
            $expected,
            PropertyToFieldMetadataUtil::fromPropertyStringToFieldMetadata($class, $apieContext, $property)
        );
    }

    private function createConstructorParameter(string $className, string $parameterName): ConstructorParameter
    {
        $refl = new ReflectionClass($className);
        foreach ($refl->getConstructor()->getParameters() as $parameter) {
            if ($parameter->name === $parameterName) {
                return new ConstructorParameter($parameter);
            }
        }
        throw new LogicException('Parameter ' . $parameterName . ' not found!');
    }

    private function createSetter(string $className, string $method): SetterMethod
    {
        return new SetterMethod(
            new ReflectionMethod($className, $method)
        );
    }

    private function createProperty(string $className, string $property, bool $optional = false): PublicProperty
    {
        return new PublicProperty(
            new ReflectionProperty($className, $property),
            $optional
        );
    }

    public function fieldMetadataProvider(): Generator
    {
        yield 'simple property' => [
            $this->createConstructorParameter(Order::class, 'id'),
            Order::class,
            'id'
        ];
        yield 'read only property' => [
            null,
            Order::class,
            'orderStatus'
        ];
        yield 'property on item list' => [
            $this->createConstructorParameter(OrderLine::class, 'id'),
            Order::class,
            'orderLines.0.id'
        ];
        yield 'array property' => [
            null,
            Order::class,
            'orderLines.0'
        ];
        yield 'setter only' => [
            $this->createSetter(UserWithAddress::class, 'setPassword'),
            UserWithAddress::class,
            'password'
        ];
        yield 'strings have no child' => [
            null,
            UserWithAddress::class,
            'password.something'
        ];
        yield 'item list' => [
            null,
            ItemList::class,
            '0'
        ];

        if (trait_exists(CompositeValueObject::class)) {
            yield 'composite value object' => [
                $this->createProperty(AddressWithZipcodeCheck::class, 'street', false),
                AddressWithZipcodeCheck::class,
                'street'
            ];
            yield 'composite value object, internal property' => [
                null,
                AddressWithZipcodeCheck::class,
                'manual'
            ];
        }
    }
}
