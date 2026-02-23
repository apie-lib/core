<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\FileUri;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use PHPUnit\Framework\TestCase;

class FileUriTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    #[\PHPUnit\Framework\Attributes\DataProvider('inputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function fromNative_allows_all_strings_that_are_not_too_long(string $expected, string $input)
    {
        $testItem = FileUri::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('inputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_all_strings_that_are_not_too_long(string $expected, string $input)
    {
        $testItem = new FileUri($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public static function inputProvider()
    {
        yield 'regular url' => ['http://www.test.nl', 'http://www.test.nl'];
        yield 'localhost' => ['http://localhost', 'http://localhost'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_refuses_invalid_file_names(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new FileUri($input);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_refuses_invalid_file_names_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        FileUri::fromNative($input);
    }

    public static function invalidProvider()
    {
        yield 'too long' => [str_repeat('1', 256)];
        yield 'null character is always invalid' => [chr(0) . 'test'];
        yield 'with linux path' => ['test/test.txt'];
        yield 'with windows path' => ['c:\system/test.txt'];
        yield 'empty string' => [''];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            FileUri::class,
            'FileUri-post',
            [
                'type' => 'string',
                'format' => 'fileuri',
                'description' => true,
            ]
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(FileUri::class, interval: 10);
    }
}
