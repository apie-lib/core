<?php

namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ApieLib;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\ValueObjects\IdentifierUri;
use Apie\Fixtures\Entities\ImageFile;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\UserWithAddress;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use PHPUnit\Framework\TestCase;

class IdentifierUriTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;
    protected function setUp(): void
    {
        $lists = [
            UserWithAddress::class,
            ImageFile::class,
            Order::class,
        ];
        ApieLib::registerAlias(
            EntityInterface::class,
            implode('|', $lists)
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            IdentifierUri::class,
            'IdentifierUri-post',
            [
                'type' => 'string',
                'pattern' => true,
                'format' => 'identifieruri'
            ]
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_merges_the_regular_expression()
    {
        $this->markTestSkipped('it does not merge regular expressions yet');
        $actual = IdentifierUri::getRegularExpression();
        $expected = '';
        $this->assertEquals($expected, $actual);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(IdentifierUri::class);
    }
}
