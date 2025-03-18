<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ValueObjects\DatabaseText;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use PHPUnit\Framework\TestCase;

class DatabaseTextTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    #[\PHPUnit\Framework\Attributes\DataProvider('inputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function fromNative_allows_all_strings_that_are_not_too_long(string $expected, string $input)
    {
        $testItem = DatabaseText::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('inputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_all_strings_that_are_not_too_long(string $expected, string $input)
    {
        $testItem = new DatabaseText($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public static function inputProvider()
    {
        yield ['', '    '];
        yield ['', str_repeat(' ', 70000)];
        yield ['test', 'test'];
        yield ['trimmed', '   trimmed   '];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_refuses_strings_that_are_too_long(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new DatabaseText($input);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_refuses_strings_that_are_too_long_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        DatabaseText::fromNative($input);
    }

    public static function invalidProvider()
    {
        yield [str_repeat('1', '70000')];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            DatabaseText::class,
            'DatabaseText-post',
            [
                'type' => 'string',
                'minLength' => 0,
                'maxLength' => 65535,
                'example' => 'Lorem Ipsum',
            ]
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(DatabaseText::class);
    }
}
