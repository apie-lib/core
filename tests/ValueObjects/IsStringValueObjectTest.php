<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\ValueObjects\IsStringValueObjectExample;
use PHPUnit\Framework\TestCase;
use Stringable;

class IsStringValueObjectTest extends TestCase implements Stringable
{
    public function testFromNative()
    {
        $this->assertEquals(
            'text',
            IsStringValueObjectExample::fromNative('text')->__toString()
        );

        $this->assertEquals(
            'example',
            IsStringValueObjectExample::fromNative($this)->toNative()
        );

        $this->assertEquals(
            '12',
            IsStringValueObjectExample::fromNative(12)->__toString()
        );

        $this->assertEquals(
            '12.5',
            IsStringValueObjectExample::fromNative(12.5)->__toString()
        );

        $this->assertEquals(
            'true',
            IsStringValueObjectExample::fromNative(true)->toNative()
        );

        $this->assertEquals(
            'false',
            IsStringValueObjectExample::fromNative(false)->__toString()
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidInputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_provide_a_validation_check(mixed $invalidValue): void
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        IsStringValueObjectExample::fromNative($invalidValue);
    }

    public static function invalidInputProvider()
    {
        yield [''];
        yield [' '];
        yield [0];
        yield ['0'];
    }
    
    public function __toString(): string
    {
        return 'example';
    }
}
