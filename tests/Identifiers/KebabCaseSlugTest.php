<?php
namespace Apie\Tests\Core\Identifiers;

use Apie\Core\Identifiers\KebabCaseSlug;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use Generator;
use PHPUnit\Framework\TestCase;

class KebabCaseSlugTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;
    /**
     * @test
     * @dataProvider inputProvider
     */
    public function fromNative_allows_many_names(string $expected, string $input)
    {
        $testItem = KebabCaseSlug::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function it_allows_many_names(string $expected, string $input)
    {
        $testItem = new KebabCaseSlug($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public function inputProvider()
    {
        yield ['slug-example', 'slug-example'];
        yield ['short', 'short'];
        yield ['example-3-example3', 'example-3-example3'];
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_non_kebab_case_strings(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new KebabCaseSlug($input);
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_non_kebab_case_strings_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        KebabCaseSlug::fromNative($input);
    }

    public function invalidProvider()
    {
        yield ['pascal_case_slug'];
        yield ['Capital-start'];
        yield ["capital-Start"];
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(KebabCaseSlug::class);
    }

    /**
     * @test
     */
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            KebabCaseSlug::class,
            'KebabCaseSlug-post',
            [
                'type' => 'string',
                'format' => 'kebabcaseslug',
                'pattern' => true,
            ]
        );
    }

    /**
     * @test
     * @dataProvider otherFormatsProvider
     */
    public function it_can_be_converted_in_other_slug_formats(string $expected, string $input, string $methodCall)
    {
        $testItem = new KebabCaseSlug($input);
        $actual = $testItem->$methodCall()->toNative();
        $this->assertEquals($expected, $actual);
    }

    public function otherFormatsProvider(): Generator
    {
        yield [
            'slugExample',
            'slug-example',
            'toCamelCaseSlug'
        ];
        yield [
            'short',
            'short',
            'toCamelCaseSlug',
        ];
        yield [
            'example3Example3',
            'example3-example3',
            'toCamelCaseSlug',
        ];

        yield [
            'SlugExample',
            'slug-example',
            'toPascalCaseSlug'
        ];
        yield [
            'Short',
            'short',
            'toPascalCaseSlug',
        ];
        yield [
            'Example3Example3',
            'example3-example3',
            'toPascalCaseSlug',
        ];

        
        yield [
            'slug_example',
            'slug-example',
            'toSnakeCaseSlug'
        ];
        yield [
            'short',
            'short',
            'toSnakeCaseSlug',
        ];
        yield [
            'example3_example3',
            'example3-example3',
            'toSnakeCaseSlug',
        ];
    }
}
