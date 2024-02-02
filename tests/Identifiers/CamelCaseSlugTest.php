<?php
namespace Apie\Tests\Core\Identifiers;

use Apie\Core\Identifiers\CamelCaseSlug;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use Generator;
use PHPUnit\Framework\TestCase;

class CamelCaseSlugTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function fromNative_allows_many_names(string $expected, string $input)
    {
        $testItem = CamelCaseSlug::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function it_allows_many_names(string $expected, string $input)
    {
        $testItem = new CamelCaseSlug($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public function inputProvider()
    {
        yield ['slugExample', 'slugExample'];
        yield ['short', 'short'];
        yield ['example3Example3', 'example3Example3'];
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_non_pascal_case_strings(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new CamelCaseSlug($input);
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_non_pascal_case_strings_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        CamelCaseSlug::fromNative($input);
    }

    public function invalidProvider()
    {
        yield ['kebab-case-slug'];
        yield ['Capital_start'];
        yield ["capital_Start"];
    }

    /**
     * @test
     */
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            CamelCaseSlug::class,
            'CamelCaseSlug-post',
            [
                'type' => 'string',
                'format' => 'camelcaseslug',
                'pattern' => true,
            ]
        );
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(CamelCaseSlug::class);
    }

    /**
     * @test
     * @dataProvider otherFormatsProvider
     */
    public function it_can_be_converted_in_other_slug_formats(string $expected, string $input, string $methodCall)
    {
        $testItem = new CamelCaseSlug($input);
        $actual = $testItem->$methodCall()->toNative();
        $this->assertEquals($expected, $actual);
    }

    public function otherFormatsProvider(): Generator
    {
        yield [
            'slug-example',
            'slugExample',
            'toKebabCaseSlug'
        ];
        yield [
            'short',
            'short',
            'toKebabCaseSlug',
        ];
        yield [
            'example3-example3',
            'example3Example3',
            'toKebabCaseSlug',
        ];

        yield [
            'SlugExample',
            'slugExample',
            'toPascalCaseSlug'
        ];
        yield [
            'Short',
            'short',
            'toPascalCaseSlug',
        ];
        yield [
            'Example3Example3',
            'example3Example3',
            'toPascalCaseSlug',
        ];

        yield [
            'slug_example',
            'slugExample',
            'toSnakeCaseSlug'
        ];
        yield [
            'short',
            'short',
            'toSnakeCaseSlug',
        ];
        yield [
            'example3_example3',
            'example3Example3',
            'toSnakeCaseSlug',
        ];

    }
}
