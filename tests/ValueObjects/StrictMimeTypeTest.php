<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\RegexUtils;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\StrictMimeType;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use PHPUnit\Framework\TestCase;

class StrictMimeTypeTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function fromNative_allows_only_mimetypes(string $expected, string $input)
    {
        $testItem = StrictMimeType::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function it_allows_only_mimetypes(string $expected, string $input)
    {
        $testItem = new StrictMimeType($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public function inputProvider()
    {
        yield 'plain text mime type' => ['text/plain', 'text/plain'];
        yield 'binary stream mime type' => ['application/octet-stream', 'application/octet-stream'];
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_invalid_file_names(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new StrictMimeType($input);
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_invalid_file_names_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        StrictMimeType::fromNative($input);
    }

    public function invalidProvider()
    {
        yield 'wild card mime type' => ['image/*'];
        yield 'no slash' => ['applicationjson'];
        yield 'empty string' => [''];
    }

    /**
     * @test
     */
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            StrictMimeType::class,
            'StrictMimeType-post',
            [
                'type' => 'string',
                'format' => 'strictmimetype',
                'pattern' => RegexUtils::removeDelimiters(StrictMimeType::getRegularExpression()),
            ]
        );
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(StrictMimeType::class);
    }
}
