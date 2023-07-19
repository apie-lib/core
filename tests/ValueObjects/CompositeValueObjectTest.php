<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Fixtures\Enums\ColorEnum;
use Apie\Fixtures\Incorrect\ValueObjects\CompositeValueObjectWithIntersectionTypehint;
use Apie\Fixtures\Incorrect\ValueObjects\CompositeValueObjectWithoutTypehint;
use Apie\Fixtures\TestHelpers\TestValidationError;
use Apie\Fixtures\ValueObjects\CompositeValueObjectExample;
use Apie\Fixtures\ValueObjects\CompositeValueObjectWithOptionalFields;
use Apie\Fixtures\ValueObjects\CompositeValueObjectWithUnionType;
use Generator;
use PHPUnit\Framework\TestCase;

class CompositeValueObjectTest extends TestCase
{
    use TestValidationError;
    /**
     * @test
     */
    public function it_can_create_a_value_object_from_an_array()
    {
        $testItem = CompositeValueObjectExample::fromNative([
            'string' => 12,
            'integer' => 42,
            'floatingPoint' => 1.5,
            'trueOrFalse' => true,
            'mixed' => [],
            'color' => ColorEnum::RED->value,
        ]);
        $this->assertEquals('12', $testItem->getString());
        $this->assertEquals(42, $testItem->getInteger());
        $this->assertEquals(1.5, $testItem->getFloatingPoint());
        $this->assertEquals(true, $testItem->getTrueOrFalse());
        $this->assertEquals([], $testItem->getMixed());
        $this->assertEquals(ColorEnum::RED, $testItem->getColor());
    }

    /**
     * @test
     * @dataProvider unionDataProvider
     */
    public function it_can_process_union_types(string|int $expectedStringFirst, string|int $expectedIntFirst, array $input)
    {
        $testItem = CompositeValueObjectWithUnionType::fromNative($input);
        $this->assertSame($expectedStringFirst, $testItem->getStringFirst());
        $this->assertSame($expectedIntFirst, $testItem->getIntFirst());
    }

    public function unionDataProvider()
    {
        yield 'strings only' => [
            'text',
            'test',
            ['stringFirst' => 'text', 'intFirst' => 'test'],
        ];

        yield 'integer' => [
            12,
            12,
            ['stringFirst' => 12, 'intFirst' => 12],
        ];

        yield 'floating point' => [
            '1.5',
            '1.5',
            ['stringFirst' => 1.5, 'intFirst' => 1.5],
        ];

        yield 'integer text' => [
            '12',
            '12',
            ['stringFirst' => '12', 'intFirst' => '12'],
        ];
    }

    /**
     * @test
     * @dataProvider optionalProvider
     */
    public function it_can_process_optional_properties(array $expected, array $input)
    {
        $testItem = CompositeValueObjectWithOptionalFields::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public function optionalProvider(): Generator
    {
        yield 'empty input' => [
            ['withDefaultValue' => 'default value'],
            []
        ];
        yield 'empty field input' => [
            ['withDefaultValue' => 'default value', 'withOptionalAttribute' => ''],
            ['withOptionalAttribute' => '']
        ];
        yield 'explicit null' => [
            ['withDefaultValue' => null],
            ['withDefaultValue' => null]
        ];

        yield 'internal properties are ignored' => [
            ['withDefaultValue' => 'default value'],
            ['isInternal' => '42']
        ];
    }

    /**
     * @test
     * @dataProvider incorrectObjectsProvider
     */
    public function it_throws_an_error_if_object_has_incorrect_properties(
        array $expectedErrorMessages,
        string $className,
        array $input
    ): void {
        $this->assertValidationError(
            $expectedErrorMessages,
            function () use ($className, &$input) {
                $className::fromNative($input);
            }
        );
    }

    public function incorrectObjectsProvider(): iterable
    {
        yield 'no typehint, property provided' => [
            ['noTypehint' => 'Type (null) is not expected, expected ReflectionUnionType|ReflectionNamedType'],
            CompositeValueObjectWithoutTypehint::class,
            ['noTypehint' => 42],
        ];
        yield 'intersection typehint, property provided' => [
            ['intersection' =>  'Type (object "Apie\Core\ValueObjects\Interfaces\ValueObjectInterface&Apie\Core\Entities\EntityInterface") is not expected, expected ReflectionUnionType|ReflectionNamedType'],
            CompositeValueObjectWithIntersectionTypehint::class,
            ['intersection' => 42],
        ];
        yield 'intersection typehint, no property provided' => [
            ['intersection' => 'Type (object "Apie\Core\ValueObjects\Interfaces\ValueObjectInterface&Apie\Core\Entities\EntityInterface") is not expected, expected ReflectionUnionType|ReflectionNamedType'],
            CompositeValueObjectWithIntersectionTypehint::class,
            [],
        ];
    }
}
