<?php
namespace Apie\Tests\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\Core\Indexing\Indexer;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\OrderLine;
use Apie\Fixtures\Entities\UserWithAddress;
use Apie\Fixtures\Identifiers\OrderIdentifier;
use Apie\Fixtures\Identifiers\OrderLineIdentifier;
use Apie\Fixtures\Identifiers\UserWithAddressIdentifier;
use Apie\Fixtures\Lists\OrderLineList;
use Apie\Fixtures\ValueObjects\AddressWithZipcodeCheck;
use Apie\Fixtures\ValueObjects\IsStringValueObjectExample;
use Apie\TextValueObjects\DatabaseText;
use Generator;
use PHPUnit\Framework\TestCase;

class IndexerTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideIndexes
     */
    public function it_indexes_strings(array $expected, mixed $input)
    {
        $testItem = Indexer::create();
        $this->assertEquals(
            $expected,
            $testItem->getIndexesForObject($input, new ApieContext())
        );
    }

    public function provideIndexes(): Generator
    {
        yield [
            [
                'test' => 1,
            ],
            new IsStringValueObjectExample('test'),
        ];
        yield [
            [
                '5d75a0aa-01a2-48f1-b0f0-57383fb1ad5f' => 1,
            ],
            new UserWithAddress(
                new AddressWithZipcodeCheck(
                    new DatabaseText('street'),
                    new DatabaseText('12'),
                    new DatabaseText('1011AA'),
                    new DatabaseText('Amsterdam')
                ),
                UserWithAddressIdentifier::fromNative('5d75a0aa-01a2-48f1-b0f0-57383fb1ad5f')
            )
        ];
        yield [
            [
                '6679805d-2059-4e2e-b3ef-45b6a752cc65' => 2,
                'da5c403e-3eb9-4f3d-affc-f634e095e45f' => 1,
                '9ebebe8a-bb37-4466-8154-5fad0cc08312' => 1,
            ],
            new Order(
                OrderIdentifier::fromNative('6679805d-2059-4e2e-b3ef-45b6a752cc65'),
                new OrderLineList([
                    new OrderLine(OrderLineIdentifier::fromNative('da5c403e-3eb9-4f3d-affc-f634e095e45f')),
                    new OrderLine(OrderLineIdentifier::fromNative('9ebebe8a-bb37-4466-8154-5fad0cc08312'))
                ])
            )
        ];
    }
}
