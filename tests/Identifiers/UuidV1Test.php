<?php
namespace Apie\Tests\Core\Identifiers;

use Apie\Core\Identifiers\UuidV1;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use PHPUnit\Framework\TestCase;

class UuidV1Test extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    #[\PHPUnit\Framework\Attributes\DataProvider('inputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function fromNative_allows_many_names(string $expected, string $input)
    {
        $testItem = UuidV1::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('inputProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_many_names(string $expected, string $input)
    {
        $testItem = new UuidV1($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public static function inputProvider()
    {
        yield ['123e4567-e89b-12d3-a456-426614174000', '123e4567-e89b-12d3-a456-426614174000'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_refuses_non_uuidV1_strings(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new UuidV1($input);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_refuses_non_uuidV1_strings_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        UuidV1::fromNative($input);
    }

    public static function invalidProvider()
    {
        yield ['pascal_case_slug'];
        yield ['123e4567-g89b-12d3-a456-426614179000'];
        yield ['123e4567-e89b-12d3-a456-4266141740001'];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            UuidV1::class,
            'UuidV1-post',
            [
                'type' => 'string',
                'format' => 'uuidv1',
                'pattern' => true,
            ]
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(UuidV1::class);
    }
}
