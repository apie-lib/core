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

    #[\PHPUnit\Framework\Attributes\DataProvider('fromTextProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function fromText_converts_human_text_to_snake_case(string $expected, string $input)
    {
        $testItem = SnakeCaseSlug::fromText($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public static function fromTextProvider(): Generator
    {
        yield ['hello_world', 'Hello world'];
        yield ['snake_case_slug', 'SnakeCaseSlug'];
        yield ['multiple_words_together', 'Multiple   words   together'];
        yield ['already_snake_case', 'already_snake_case'];
        yield ['trailing_and_leading', '  Trailing and leading  '];
        yield ['numbers_123', 'Numbers 123'];
        yield ['special_chars', 'Special!@#Chars'];
        yield ['no_double_underscore', 'No__Double___Underscore'];
        yield ['mixed_case_and_numbers_42', 'MixedCase and Numbers 42'];
        yield ['a_b_c', 'A---B---C'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('inputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function fromNative_allows_many_names(string $expected, string $input)
    {
        $testItem = SnakeCaseSlug::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('inputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_many_names(string $expected, string $input)
    {
        $testItem = new SnakeCaseSlug($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public static function inputProvider()
    {
        yield ['slug_example', 'slug_example'];
        yield ['short', 'short'];
        yield ['example_3_example3', 'example_3_example3'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_refuses_non_snake_case_strings(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new SnakeCaseSlug($input);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_refuses_non_snake_case_strings_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        SnakeCaseSlug::fromNative($input);
    }

    public static function invalidProvider()
    {
        yield ['kebab-case-slug'];
        yield ['CapitalStart'];
        yield ["capitalStart"];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            SnakeCaseSlug::class,
            'SnakeCaseSlug-post',
            [
                'type' => 'string',
                'format' => 'snakecaseslug',
                'pattern' => true,
                'description' => true,
            ]
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(SnakeCaseSlug::class);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('otherFormatsProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_be_converted_in_other_slug_formats(string $expected, string $input, string $methodCall)
    {
        $testItem = new SnakeCaseSlug($input);
        $actual = $testItem->$methodCall()->toNative();
        $this->assertEquals($expected, $actual);
    }

    public static function otherFormatsProvider(): Generator
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
