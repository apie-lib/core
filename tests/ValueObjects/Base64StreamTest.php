<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ValueObjects\Base64Stream;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use PHPUnit\Framework\TestCase;

class Base64StreamTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    #[\PHPUnit\Framework\Attributes\DataProvider('inputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function fromNative_allows_all_strings(string $expected, string $input)
    {
        $testItem = Base64Stream::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('inputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_all_strings(string $expected, string $input)
    {
        $testItem = new Base64Stream($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public static function inputProvider()
    {
        yield 'empty string' => ['', ''];
        yield 'space' => ['', ' '];
        yield 'regular base 64' => ['AAaa', 'AAaa'];
        yield 'base 64 with spaces' => ['AAaa', ' A A a a'];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            Base64Stream::class,
            'Base64Stream-post',
            [
                'type' => 'string',
                'format' => 'base64',
                'pattern' => true,
            ]
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(Base64Stream::class);
    }
}
