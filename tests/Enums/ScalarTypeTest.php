<?php
namespace Apie\Tests\Core\Enums;

use Apie\Core\Enums\ScalarType;
use Apie\Core\Identifiers\Identifier;
use Apie\Core\Identifiers\Ulid;
use Apie\Fixtures\TestHelpers\ObjectTestCase;
use Apie\TypeConverter\ReflectionTypeFactory;
use PHPUnit\Framework\Attributes\DataProvider;

class ScalarTypeTest extends ObjectTestCase
{
    public static function className(): string
    {
        return ScalarType::class;
    }

    public static function getOpenApiSchemaForCreation(): array
    {
        return [
            'type' => 'string',
            'enum' => [
                ScalarType::STRING->value,
                ScalarType::FLOAT->value,
                ScalarType::INTEGER->value,
                ScalarType::NULLVALUE->value,
                ScalarType::ARRAY->value,
                ScalarType::BOOLEAN->value,
                ScalarType::MIXED->value,
                ScalarType::STDCLASS->value,
            ],
        ];
    }

    #[DataProvider('createFromReflectionTypeDataProvider')]
    public function testCreateFromReflectionType(
        ScalarType $expected,
        string $typehint,
        bool $ignoreNull
    ) {
        $this->assertEquals(
            $expected,
            ScalarType::createFromReflectionType(
                $typehint ? ReflectionTypeFactory::createReflectionType($typehint) : null,
                $ignoreNull
            )
        );
    }

    public static function createFromReflectionTypeDataProvider(): array
    {
        return [
            'no typehint' => [ScalarType::MIXED, '', false],
            'no typehint ignore null' => [ScalarType::MIXED, '', true],
            'string' => [ScalarType::STRING, 'string', false],
            'float' => [ScalarType::FLOAT, 'float', false],
            'integer' => [ScalarType::INTEGER, 'int', false],
            'null' => [ScalarType::NULLVALUE, 'null', false],
            'array' => [ScalarType::ARRAY, 'array', false],
            'boolean' => [ScalarType::BOOLEAN, 'bool', false],
            'mixed' => [ScalarType::MIXED, 'mixed', false],
            'stdClass' => [ScalarType::STDCLASS, 'stdClass', false],
            'union of incompatible types' => [ScalarType::MIXED, 'string|int', false],
            'intersection type' => [ScalarType::STDCLASS, 'stdClass&' . __CLASS__, false],
        ];
    }

    #[DataProvider('toDoctrineTypeDataProvider')]
    public function testToDoctrineType(ScalarType $scalarType, string $expected): void
    {
        $this->assertSame($expected, $scalarType->toDoctrineType());
    }

    public static function toDoctrineTypeDataProvider(): array
    {
        return [
            'string' => [ScalarType::STRING, 'string'],
            'float' => [ScalarType::FLOAT, 'float'],
            'integer' => [ScalarType::INTEGER, 'integer'],
            'null' => [ScalarType::NULLVALUE, 'null'],
            'array' => [ScalarType::ARRAY, 'array'],
            'boolean' => [ScalarType::BOOLEAN, 'bool'],
            'mixed' => [ScalarType::MIXED, 'mixed'],
            'stdClass' => [ScalarType::STDCLASS, 'json'],
        ];
    }

    #[DataProvider('toJsonSchemaTypeDataProvider')]
    public function testToJsonSchemaType(ScalarType $scalarType, string $expected): void
    {
        $this->assertSame($expected, $scalarType->toJsonSchemaType());
    }

    public static function toJsonSchemaTypeDataProvider(): array
    {
        return [
            'string' => [ScalarType::STRING, 'string'],
            'float' => [ScalarType::FLOAT, 'number'],
            'integer' => [ScalarType::INTEGER, 'integer'],
            'null' => [ScalarType::NULLVALUE, 'null'],
            'array' => [ScalarType::ARRAY, 'array'],
            'boolean' => [ScalarType::BOOLEAN, 'boolean'],
            'mixed' => [ScalarType::MIXED, 'mixed'],
            'stdClass' => [ScalarType::STDCLASS, 'object'],
        ];
    }
}