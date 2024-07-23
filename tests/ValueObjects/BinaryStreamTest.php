<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ValueObjects\BinaryStream;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use PHPUnit\Framework\TestCase;

class BinaryStreamTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function fromNative_allows_all_strings(string $expected, string $input)
    {
        $testItem = BinaryStream::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function it_allows_all_strings(string $expected, string $input)
    {
        $testItem = new BinaryStream($input);
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
     */
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            BinaryStream::class,
            'BinaryStream-post',
            [
                'type' => 'string',
                'format' => 'binary',
            ]
        );
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(BinaryStream::class);
    }
}
