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

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(Ulid::class);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideValidInput')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_fromNative(string $input)
    {
        $testItem = Ulid::fromNative($input);
        $this->assertEquals($input, $testItem->toNative());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideValidInput')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_construct(string $input)
    {
        $testItem = new Ulid($input);
        $this->assertEquals($input, $testItem->toNative());
    }

    public static function provideValidInput(): Generator
    {
        yield 'regular' => ['aAadSRraAadSRraAadSRrR'];
    }



    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            Ulid::class,
            'Ulid-post',
            [
                'type' => 'string',
                'format' => 'ulid',
                'pattern' => true,
                'description' => true,
            ]
        );
    }
}
