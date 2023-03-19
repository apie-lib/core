<?php
namespace Apie\Tests\Core\Lists;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\Lists\LazyLoadedList;
use Apie\Core\Datalayers\ValueObjects\LazyLoadedListIdentifier;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Identifiers\OrderIdentifier;
use Apie\Fixtures\Lists\OrderLineList;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class LazyLoadedListTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_return_objects_from_a_list()
    {
        $id = LazyLoadedListIdentifier::createFrom(new BoundedContextId('default'), new ReflectionClass(Order::class));
        $orders = [];
        for ($i = 0; $i < 100; $i++) {
            $orders[] = new Order(OrderIdentifier::createRandom(), new OrderLineList());
        }
    
        $testItem = LazyLoadedList::createFromArray($id, $orders);
        $this->assertSame($orders[0], $testItem->get(0));
        $this->assertSame($orders[99], $testItem->get(99));
        $this->assertEquals([$orders[0], $orders[1]], $testItem->take(0, 2));
        $this->assertEquals(100, $testItem->totalCount());
    }

    /**
     * @test
     */
    public function it_can_filter_objects_from_a_list()
    {
        $id = LazyLoadedListIdentifier::createFrom(new BoundedContextId('default'), new ReflectionClass(Order::class));
        $orders = [];
        for ($i = 0; $i < 100; $i++) {
            $orders[] = new Order(OrderIdentifier::createRandom(), new OrderLineList());
        }
    
        $testItem = LazyLoadedList::createFromArray($id, $orders);
        $counter = 0;
        $this->assertEquals(50, $testItem->filterList(function ($object) use (&$counter) {
            return (bool) ($counter++ & 1);
        })->totalCount());
    }
}
