<?php
namespace Apie\Tests\Core;

use Apie\Core\TypeUtils;
use Apie\Core\ValueObjects\DatabaseText;
use Apie\TextValueObjects\NonEmptyString;
use Apie\TypeConverter\ReflectionTypeFactory;
use Generator;
use PHPUnit\Framework\TestCase;
use ReflectionType;

class TypeUtilsTest extends TestCase
{
    /**
     * @test
     * @dataProvider emptyStringAllowedProvider
     */
    public function it_can_determine_if_empty_strings_are_allowed(bool $expected, ?ReflectionType $input)
    {
        $this->assertEquals($expected, TypeUtils::allowEmptyString($input));
    }

    public function emptyStringAllowedProvider(): Generator
    {
        yield 'no typehint' => [true, null];
        yield 'string' => [true, ReflectionTypeFactory::createReflectionType('string')];
        yield 'nullable string' => [true, ReflectionTypeFactory::createReflectionType('?string')];
        yield 'integer' => [false, ReflectionTypeFactory::createReflectionType('int')];
        yield 'nullable integer' => [false, ReflectionTypeFactory::createReflectionType('?int')];
        yield 'floating point' => [false, ReflectionTypeFactory::createReflectionType('float')];
        yield 'nullable floating point' => [false, ReflectionTypeFactory::createReflectionType('?float')];
        yield 'mixed' => [true, ReflectionTypeFactory::createReflectionType('mixed')];
        if (class_exists(NonEmptyString::class)) {
            yield 'non empty string value object' => [false, ReflectionTypeFactory::createReflectionType(NonEmptyString::class)];
            yield 'intersection type with value object' => [false, ReflectionTypeFactory::createReflectionType(NonEmptyString::class . '&' . DatabaseText::class)];
        }
        yield 'non-value object' => [false, ReflectionTypeFactory::createReflectionType(__CLASS__)];
        yield 'value object with empty string allowed' => [true, ReflectionTypeFactory::createReflectionType(DatabaseText::class)];
        yield 'union type with value object' => [true, ReflectionTypeFactory::createReflectionType('null|' . DatabaseText::class)];
       
    }
}
