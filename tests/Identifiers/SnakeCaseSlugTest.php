<?php
namespace Apie\Tests\Core\Identifiers;

use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use Generator;
use PHPUnit\Framework\TestCase;

class SnakeCaseSlugTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function fromNative_allows_many_names(string $expected, string $input)
    {
        $testItem = SnakeCaseSlug::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function it_allows_many_names(string $expected, string $input)
    {
        $testItem = new SnakeCaseSlug($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public function inputProvider()
    {
        yield ['slug_example', 'slug_example'];
        yield ['short', 'short'];
        yield ['example_3_example3', 'example_3_example3'];
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_non_snake_case_strings(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new SnakeCaseSlug($input);
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_non_snake_case_strings_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        SnakeCaseSlug::fromNative($input);
    }

    public function invalidProvider()
    {
        yield ['kebab-case-slug'];
        yield ['CapitalStart'];
        yield ["capitalStart"];
    }

    /**
     * @test
     */
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            SnakeCaseSlug::class,
            'SnakeCaseSlug-post',
            [
                'type' => 'string',
                'format' => 'snakecaseslug',
                'pattern' => true,
            ]
        );
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(SnakeCaseSlug::class);
    }

    /**
     * @test
     * @dataProvider otherFormatsProvider
     */
    public function it_can_be_converted_in_other_slug_formats(string $expected, string $input, string $methodCall)
    {
        $testItem = new SnakeCaseSlug($input);
        $actual = $testItem->$methodCall()->toNative();
        $this->assertEquals($expected, $actual);
    }

    public function otherFormatsProvider(): Generator
    {
        yield [
            'slugExample',
            'slug_example',
            'toCamelCaseSlug'
        ];
        yield [
            'short',
            'short',
            'toCamelCaseSlug',
        ];
        yield [
            'example3Example3',
            'example3_example3',
            'toCamelCaseSlug',
        ];

        yield [
            'SlugExample',
            'slug_example',
            'toPascalCaseSlug'
        ];
        yield [
            'Short',
            'short',
            'toPascalCaseSlug',
        ];
        yield [
            'Example3Example3',
            'example3_example3',
            'toPascalCaseSlug',
        ];

        
        yield [
            'slug-example',
            'slug_example',
            'toKebabCaseSlug'
        ];
        yield [
            'short',
            'short',
            'toKebabCaseSlug',
        ];
        yield [
            'example3-example3',
            'example3_example3',
            'toKebabCaseSlug',
        ];
    }
}
