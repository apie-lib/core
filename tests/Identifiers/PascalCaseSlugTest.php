<?php
namespace Apie\Tests\Core\Identifiers;

use Apie\Core\Identifiers\PascalCaseSlug;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use Generator;
use PHPUnit\Framework\TestCase;

class PascalCaseSlugTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function fromNative_allows_many_names(string $expected, string $input)
    {
        $testItem = PascalCaseSlug::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function it_allows_many_names(string $expected, string $input)
    {
        $testItem = new PascalCaseSlug($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public function inputProvider()
    {
        yield ['SlugExample', 'SlugExample'];
        yield ['Short', 'Short'];
        yield ['Example3Example3', 'Example3Example3'];
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_non_pascal_case_strings(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new PascalCaseSlug($input);
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_non_pascal_case_strings_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        PascalCaseSlug::fromNative($input);
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
            PascalCaseSlug::class,
            'PascalCaseSlug-post',
            [
                'type' => 'string',
                'format' => 'pascalcaseslug',
                'pattern' => true,
            ]
        );
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(PascalCaseSlug::class);
    }

    /**
     * @test
     * @dataProvider otherFormatsProvider
     */
    public function it_can_be_converted_in_other_slug_formats(string $expected, string $input, string $methodCall)
    {
        $testItem = new PascalCaseSlug($input);
        $actual = $testItem->$methodCall()->toNative();
        $this->assertEquals($expected, $actual);
    }

    public function otherFormatsProvider(): Generator
    {
        yield [
            'slug-example',
            'SlugExample',
            'toKebabCaseSlug'
        ];
        yield [
            'short',
            'Short',
            'toKebabCaseSlug',
        ];
        yield [
            'example3-example3',
            'Example3Example3',
            'toKebabCaseSlug',
        ];

        yield [
            'slug_example',
            'SlugExample',
            'toSnakeCaseSlug'
        ];
        yield [
            'short',
            'Short',
            'toSnakeCaseSlug',
        ];
        yield [
            'example3_example3',
            'Example3Example3',
            'toSnakeCaseSlug',
        ];

        
        yield [
            'slugExample',
            'SlugExample',
            'toCamelCaseSlug'
        ];
        yield [
            'short',
            'Short',
            'toCamelCaseSlug',
        ];
        yield [
            'example3Example3',
            'Example3Example3',
            'toCamelCaseSlug',
        ];
    }
}
