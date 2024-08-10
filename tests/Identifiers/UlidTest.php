<?php
namespace Apie\Tests\Core\Identifiers;

use Apie\Core\Identifiers\Ulid;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use Generator;
use PHPUnit\Framework\TestCase;

class UlidTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(Ulid::class);
    }

    /**
     * @test
     * @dataProvider provideValidInput
     */
    public function it_works_with_fromNative(string $input)
    {
        $testItem = Ulid::fromNative($input);
        $this->assertEquals($input, $testItem->toNative());
    }

    /**
     * @test
     * @dataProvider provideValidInput
     */
    public function it_works_with_construct(string $input)
    {
        $testItem = new Ulid($input);
        $this->assertEquals($input, $testItem->toNative());
    }

    public static function provideValidInput(): Generator
    {
        yield 'regular' => ['aAadSRraAadSRraAadSRrR'];
    }



    /**
     * @test
     */
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            Ulid::class,
            'Ulid-post',
            [
                'type' => 'string',
                'format' => 'ulid',
                'pattern' => true,
            ]
        );
    }
}
