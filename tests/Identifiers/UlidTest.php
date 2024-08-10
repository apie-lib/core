<?php
namespace Apie\Tests\Core\Identifiers;

use Apie\Core\Identifiers\Ulid;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
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