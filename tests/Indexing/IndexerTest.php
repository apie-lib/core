<?php
namespace Apie\Tests\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Indexing\Indexer;
use Apie\Core\ValueObjects\DatabaseText;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\OrderLine;
use Apie\Fixtures\Entities\UserWithAddress;
use Apie\Fixtures\Enums\ColorEnum;
use Apie\Fixtures\Enums\NoValueEnum;
use Apie\Fixtures\Identifiers\OrderIdentifier;
use Apie\Fixtures\Identifiers\OrderLineIdentifier;
use Apie\Fixtures\Identifiers\UserWithAddressIdentifier;
use Apie\Fixtures\Lists\OrderLineList;
use Apie\Fixtures\ValueObjects\AddressWithZipcodeCheck;
use Apie\Fixtures\ValueObjects\IsStringValueObjectExample;
use DateTime;
use Generator;
use PHPUnit\Framework\TestCase;

class IndexerTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideIndexes')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_indexes_strings(array $expected, mixed $input)
    {
        $testItem = Indexer::create();
        $context = new ApieContext([
            ContextConstants::DATEFORMATS => [DateTime::ATOM]
        ]);
        $this->assertEquals(
            $expected,
            $testItem->getIndexesFor($input, $context)
        );
    }

    public static function provideIndexes(): Generator
    {
        yield 'empty string' => [
            [],
            '',
        ];
        yield 'null value' => [
            [],
            null,
        ];
        yield 'a string' => [
            ['this' => 1, 'is' => 1, 'a' => 1, 'text' => 1],
            'This is a text',
        ];
        yield 'integer' => [
            [12 => 1],
            12
        ];
        yield 'false' => [
            [0 => 1],
            false,
        ];
        yield 'true' => [
            [1 => 1],
            true,
        ];
        yield 'floating point number' => [
            ["2.5" => 1],
            2.5
        ];
        yield 'array list' => [
            ['a' => 1, 'b' => 1, 'c' => 1, 'd' => 1, 0 => 1, 1 => 1, 2 => 1],
            ['a', 'b c', 'd'],
        ];
        $handle = fopen(__FILE__, 'r');
        fclose($handle);
        yield 'php handle' => [
            [],
            $handle
        ];
        $date = new DateTime();
        yield 'DateTime object' => [
            [$date->format(DateTime::ATOM) => 1],
            $date,
        ];
        yield 'Enum' => [
            ['RED' => 1],
            NoValueEnum::RED,
        ];
        yield 'Backed enum' => [
            ['RED' => 1, 'red' => 1],
            ColorEnum::RED,
        ];
        yield 'string value object' => [
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
        yield 'provideIndex attribute used' => [
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
