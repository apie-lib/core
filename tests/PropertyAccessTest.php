<?php
namespace Apie\Tests\Core;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemList;
use Apie\Core\PropertyAccess;
use Apie\Core\ValueObjects\DatabaseText;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\OrderLine;
use Apie\Fixtures\Entities\UserWithAddress;
use Apie\Fixtures\Identifiers\OrderIdentifier;
use Apie\Fixtures\Identifiers\OrderLineIdentifier;
use Apie\Fixtures\Identifiers\UserWithAddressIdentifier;
use Apie\Fixtures\Lists\OrderLineList;
use Apie\Fixtures\ValueObjects\AddressWithZipcodeCheck;
use Apie\Fixtures\ValueObjects\Password;
use Generator;
use PHPUnit\Framework\TestCase;

class PropertyAccessTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('propertyProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_get_a_property(mixed $expected, object $object, string $property)
    {
        $apieContext = new ApieContext();
        $this->assertEquals(
            $expected,
            PropertyAccess::getPropertyValue($object, explode('.', $property), $apieContext, false)
        );
    }

    public static function propertyProvider(): Generator
    {
        $orderId = OrderIdentifier::createRandom();
        $orderLineId = OrderLineIdentifier::createRandom();
        $orderLine = new OrderLine($orderLineId);
        $orderLines = new OrderLineList([$orderLine]);
        $order = new Order($orderId, $orderLines);

        yield 'simple property' => [
            $orderLines,
            $order,
            'orderLines'
        ];
        yield 'array property' => [
            $orderLine,
            $order,
            'orderLines.0'
        ];
        yield 'property on item list' => [
            $orderLineId,
            $order,
            'orderLines.0.id'
        ];
        yield 'property on item list out of index' => [
            null,
            $order,
            'orderLines.1.id'
        ];
        
        yield 'item list' => [
            null,
            new ItemList(),
            '0'
        ];

        yield 'strings have no child' => [
            null,
            $order,
            'id.something'
        ];

        $userId = UserWithAddressIdentifier::createRandom();
        $address = new AddressWithZipcodeCheck(
            new DatabaseText('Evergreen Terrace'),
            new DatabaseText('743'),
            new DatabaseText('11111'),
            new DatabaseText('Springfield')
        );
        $user = new UserWithAddress($address, $userId);
        $user->setPassword(new Password('1Aa-bB'));

        yield 'write only property' => [
            null,
            $user,
            'password'
        ];
        yield 'composite value object, getter only is ignored' => [
            null, // TODO: should this be correct behaviour?
            $address,
            'manualAddress'
        ];
        yield 'composite value object, internal property' => [
            null,
            $address,
            'manual'
        ];
    }
}
