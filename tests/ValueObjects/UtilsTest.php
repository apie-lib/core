<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\Utils;
use Apie\Fixtures\Other\AbstractClassExample;
use Apie\Fixtures\Other\AbstractInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class UtilsTest extends TestCase
{
    public function testGetDisplayNameForValueObject()
    {
        $this->assertEquals(
            'ClassExample',
            Utils::getDisplayNameForValueObject(new ReflectionClass(AbstractClassExample::class))
        );
        $this->assertEquals(
            'ValueObject',
            Utils::getDisplayNameForValueObject(new ReflectionClass(ValueObjectInterface::class))
        );
        $this->assertEquals(
            'Abstract',
            Utils::getDisplayNameForValueObject(new ReflectionClass(AbstractInterface::class))
        );
    }

    public function test_toDate_with_DateTimeImmutable_returns_clone()
    {
        $date = new DateTimeImmutable('2023-01-01T12:00:00+00:00');
        $result = Utils::toDate($date, DateTimeImmutable::class);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertEquals($date->format(DateTimeInterface::ATOM), $result->format(DateTimeInterface::ATOM));
        $this->assertNotSame($date, $result); // Should be a clone
    }

    public function test_toDate_with_DateTime_returns_DateTimeImmutable()
    {
        $date = new DateTime('2023-01-01T12:00:00+00:00');
        $result = Utils::toDate($date);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertEquals($date->format(DateTimeInterface::ATOM), $result->format(DateTimeInterface::ATOM));
    }

    public function test_toDate_with_string_returns_DateTimeImmutable()
    {
        $isoString = '2023-01-01T12:00:00+00:00';
        $result = Utils::toDate($isoString);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertEquals($isoString, $result->format(DateTimeInterface::ATOM));
    }

    public function test_toDate_with_invalid_string_throws()
    {
        $this->expectException(InvalidTypeException::class);
        Utils::toDate('not-a-date');
    }

    public function test_toDate_with_DateTimeInterface_class_type()
    {
        $date = new DateTimeImmutable('2023-01-01T12:00:00+00:00');
        $result = Utils::toDate($date, DateTimeInterface::class);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
    }

    public function test_toArray_with_array()
    {
        $input = ['a' => 1, 'b' => 2];
        $result = Utils::toArray($input);
        $this->assertSame($input, $result);
    }

    public function test_toArray_with_iterable()
    {
        $generator = (function () {
            yield 'x' => 10;
            yield 'y' => 20;
        })();

        $result = Utils::toArray($generator);
        $this->assertSame(['x' => 10, 'y' => 20], $result);
    }

    public function test_toArray_with_invalid_type_throws()
    {
        $this->expectException(\Apie\Core\Exceptions\InvalidTypeException::class);
        Utils::toArray('not an array');
    }

    public function test_toNative_recursive_array()
    {
        $input = [
            'foo' => [
                'bar' => new class implements ValueObjectInterface {
                    public function toNative(): string {
                        return 'baz';
                    }
                    public static function fromNative(mixed $input): self {
                        throw new \LogicException('not implemented');
                    }
                }
            ]
        ];
        $result = Utils::toNative($input);
        $this->assertSame(['foo' => ['bar' => 'baz']], $result);
    }

    public function test_toNative_iterable_object()
    {
        $iterable = new \ArrayIterator(['key' => 'value']);
        $result = Utils::toNative($iterable);
        $this->assertSame(['key' => 'value'], $result);
    }

    public function test_toString_with_array_should_throw()
    {
        $this->expectException(\Apie\Core\Exceptions\InvalidTypeException::class);
        Utils::toString(['this' => 'array']);
    }

    public function test_toTypehint_array_and_iterable()
    {
        $typehint = ReflectionTypeFactory::createReflectionType('array');
        $result = Utils::toTypehint($typehint, ['a' => 1]);
        $this->assertSame(['a' => 1], $result);

        $iterable = new \ArrayIterator(['b' => 2]);
        $result2 = Utils::toTypehint($typehint, $iterable);
        $this->assertSame(['b' => 2], $result2);
    }

    public function test_toTypehint_iterable_typehint()
    {
        $typehint = ReflectionTypeFactory::createReflectionType('iterable');
        $iterable = new \ArrayIterator(['x' => 1]);
        $result = Utils::toTypehint($typehint, $iterable);
        $this->assertSame(['x' => 1], $result);
    }
}
