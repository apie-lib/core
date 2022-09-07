<?php
namespace Apie\Tests\Core\Identifiers;

use Apie\Core\Identifiers\PascalCaseSlug;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
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
        yield ['slug_example', 'slug_example'];
        yield ['short', 'short'];
        yield ['example_3_example3', 'example_3_example3'];
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_non_kebab_case_strings(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new PascalCaseSlug($input);
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_non_kebab_case_strings_with_fromNative(string $input)
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
}
