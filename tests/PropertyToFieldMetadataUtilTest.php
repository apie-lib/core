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
    #[\PHPUnit\Framework\Attributes\DataProvider('typehintProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
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

    public static function typehintProvider(): Generator
    {
        yield [true, 'string', 'string'];
        yield [true, ImageFile::class, UploadedFileInterface::class];
        yield [true, Order::class, OrderLine::class];
    }
    #[\PHPUnit\Framework\Attributes\DataProvider('fieldMetadataProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_gets_field_metadata(?FieldInterface $expected, string $className, string $property)
    {
        $apieContext = new ApieContext();
        $class = new ReflectionClass($className);
        $this->assertEquals(
            $expected,
            PropertyToFieldMetadataUtil::fromPropertyStringToFieldMetadata($class, $apieContext, $property)
        );
    }

    private static function createConstructorParameter(string $className, string $parameterName): ConstructorParameter
    {
        $refl = new ReflectionClass($className);
        foreach ($refl->getConstructor()->getParameters() as $parameter) {
            if ($parameter->name === $parameterName) {
                return new ConstructorParameter($parameter);
            }
        }
        throw new LogicException('Parameter ' . $parameterName . ' not found!');
    }

    private static function createSetter(string $className, string $method): SetterMethod
    {
        return new SetterMethod(
            new ReflectionMethod($className, $method)
        );
    }

    private static function createProperty(string $className, string $property, bool $optional = false): PublicProperty
    {
        return new PublicProperty(
            new ReflectionProperty($className, $property),
            $optional
        );
    }

    public static function fieldMetadataProvider(): Generator
    {
        yield 'simple property' => [
            self::createConstructorParameter(Order::class, 'id'),
            Order::class,
            'id'
        ];
        yield 'read only property' => [
            null,
            Order::class,
            'orderStatus'
        ];
        yield 'property on item list' => [
            self::createConstructorParameter(OrderLine::class, 'id'),
            Order::class,
            'orderLines.0.id'
        ];
        yield 'array property' => [
            null,
            Order::class,
            'orderLines.0'
        ];
        yield 'setter only' => [
            self::createSetter(UserWithAddress::class, 'setPassword'),
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
        yield 'composite value object' => [
            self::createProperty(AddressWithZipcodeCheck::class, 'street', false),
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
