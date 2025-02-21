<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\NonEmptyString;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use PHPUnit\Framework\TestCase;

class NonEmptyStringTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;
    #[\PHPUnit\Framework\Attributes\DataProvider('inputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function fromNative_allows_all_strings_that_are_not_empty(string $expected, string $input)
    {
        $testItem = NonEmptyString::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('inputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_all_strings_that_are_not_empty(string $expected, string $input)
    {
        $testItem = new NonEmptyString($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public static function inputProvider()
    {
        yield ['test', 'test'];
        yield ['trimmed', '   trimmed   '];
        yield ['test' . PHP_EOL . 'test', 'test' . PHP_EOL . 'test'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_refuses_empty_strings(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new NonEmptyString($input);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_refuses_empty_strings_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        NonEmptyString::fromNative($input);
    }

    public static function invalidProvider()
    {
        yield [''];
        yield [' '];
        yield ["          \t\n\r\n"];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            NonEmptyString::class,
            'NonEmptyString-post',
            [
                'type' => 'string',
                'format' => 'nonemptystring',
                'pattern' => true,
            ]
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(NonEmptyString::class);
    }
}
