<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\RegexUtils;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Filename;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use PHPUnit\Framework\TestCase;

class FilenameTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function fromNative_allows_all_strings_that_are_not_too_long(string $expected, string $input)
    {
        $testItem = Filename::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function it_allows_all_strings_that_are_not_too_long(string $expected, string $input)
    {
        $testItem = new Filename($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public function inputProvider()
    {
        yield 'regular filename' => ['test.txt', 'test.txt'];
        yield 'no extension' => ['readme', 'readme'];
        yield 'linux hidden file' => ['.htaccess', '.htaccess'];
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_invalid_file_names(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new Filename($input);
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_invalid_file_names_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        Filename::fromNative($input);
    }

    public function invalidProvider()
    {
        yield 'too long' => [str_repeat('1', 256)];
        yield 'null character is always invalid' => [chr(0) . 'test'];
        yield 'with linux path' => ['test/test.txt'];
        yield 'with windows path' => ['c:\system/test.txt'];
        yield 'empty string' => [''];
    }

    /**
     * @test
     */
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            Filename::class,
            'Filename-post',
            [
                'type' => 'string',
                'format' => 'filename',
                'pattern' => RegexUtils::removeDelimiters(Filename::getRegularExpression()),
            ]
        );
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(Filename::class);
    }
}
