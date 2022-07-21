<?php
namespace Apie\Tests\Core\Identifiers;

use Apie\Core\Identifiers\UuidV4;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use PHPUnit\Framework\TestCase;

class UuidV4Test extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function fromNative_allows_many_names(string $expected, string $input)
    {
        $testItem = UuidV4::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function it_allows_many_names(string $expected, string $input)
    {
        $testItem = new UuidV4($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public function inputProvider()
    {
        yield ['123e4567-e89b-12d3-a456-426614174000', '123e4567-e89b-12d3-a456-426614174000'];
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_non_uuidV4_strings(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new UuidV4($input);
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_non_uuidV4_strings_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        UuidV4::fromNative($input);
    }

    public function invalidProvider()
    {
        yield ['pascal_case_slug'];
        yield ['123e4567-g89b-12d3-a456-426614179000'];
        yield ['123e4567-e89b-12d3-a456-4266141740001'];
    }

    /**
     * @test
     */
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            UuidV4::class,
            'UuidV4-post',
            [
                'type' => 'string',
                'format' => 'uuidv4',
                'pattern' => true,
            ]
        );
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(UuidV4::class);
    }
}
