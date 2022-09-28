<?php
namespace Apie\Tests\Core\DataLayers\Grouped;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Core\Datalayers\Grouped\DataLayerByClass;
use Apie\Core\Datalayers\InMemory\InMemoryDatalayer;
use Apie\Core\Exceptions\ObjectIsImmutable;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\OrderLine;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionClass;

class DataLayerByClassTest extends TestCase
{
    use ProphecyTrait;
    /**
     * @test
     */
    public function it_can_pick_a_datalayer_for_you()
    {
        $testItem = new DataLayerByClass(
            [
                Order::class => new InMemoryDatalayer(new BoundedContextId('input')),
            ]
        );
        $otherClass = $this->prophesize(ApieDatalayer::class)->reveal();
        $testItem->setDefaultDataLayer($otherClass);
        $this->assertInstanceOf(InMemoryDatalayer::class, $testItem->pickDataLayerFor(new ReflectionClass(Order::class)));
        $this->assertSame($otherClass, $testItem->pickDataLayerFor(new ReflectionClass(OrderLine::class)));
    }

    /**
     * @test
     */
    public function it_becomes_immutable_on_setting_default_datalayer()
    {
        $testItem = new DataLayerByClass([]);
        $testItem->setDefaultDataLayer($this->prophesize(ApieDatalayer::class)->reveal());
        $this->expectException(ObjectIsImmutable::class);
        $testItem[Order::class] = new InMemoryDatalayer(new BoundedContextId('input'));
    }

    /**
     * @test
     */
    public function it_can_not_set_default_datalayer_twice()
    {
        $testItem = new DataLayerByClass([]);
        $testItem->setDefaultDataLayer($this->prophesize(ApieDatalayer::class)->reveal());
        $this->expectException(ObjectIsImmutable::class);
        $testItem->setDefaultDataLayer($this->prophesize(ApieDatalayer::class)->reveal());
    }
}
