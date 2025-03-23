<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ValueObjects\DatabaseText;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\ValueObjects\Password;
use Apie\Fixtures\ValueObjects\SnowflakeExample;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SnowflakeIdentifierTest extends TestCase
{
    #[Test]
    #[DataProvider('validProvider')]
    public function it_can_convert_from_a_string_with_fromNative(
        ?DatabaseText $expectedField,
        Password $expectedPassword,
        string $input
    ) {
        $testItem = SnowflakeExample::fromNative($input);
        $this->assertTrue($testItem instanceof SnowflakeExample);
        $this->assertEquals($input, $testItem->toNative());
        $this->assertEquals($input, $testItem->jsonSerialize());
        $this->assertEquals($input, $testItem->__toString());
        $this->assertEquals($expectedField, $testItem->getField());
        $this->assertEquals($expectedPassword, $testItem->getPassword());
    }

    public static function validProvider(): Generator
    {
        yield 'empty first part' => [null, new Password('a!A1AA'), '|a!A1AA'];
        yield 'two parts, first has newlines' => [
            new DatabaseText("test\ntest"),
            new Password('a!A1AA'),
            "test\ntest|a!A1AA"
        ];
    }

    #[Test]
    #[DataProvider('invalidProvider')]
    public function it_can_throw_error_with_fromNative(
        string $input
    ) {
        $this->expectException(InvalidStringForValueObjectException::class);
        SnowflakeExample::fromNative($input);
    }

    public static function invalidProvider(): Generator
    {
        yield 'no separator' => ['test'];
        yield 'invalid segment' => ['test|t'];
    }

    #[Test]
    public function toNative_checks_if_content_contains_no_separator_character()
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new SnowflakeExample(new DatabaseText('|'), new Password('a!A1AA'));
    }

    #[Test]
    public function it_can_generate_a_regular_expression_for_a_snowflake_id()
    {
        $expected = '^(.{0,65535})\|[^\|]+$';
        $this->assertEquals($expected, SnowflakeExample::getRegularExpression());
    }
}
