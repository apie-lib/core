<?php
namespace Apie\Tests\Core\DataLayers\Grouped;

use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Core\Datalayers\Grouped\DataLayerByClass;
use Apie\Core\Datalayers\InMemory\InMemoryDatalayer;
use Apie\Core\Exceptions\ObjectIsImmutable;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\OrderLine;
use Apie\Fixtures\TestHelpers\TestWithInMemoryDatalayer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionClass;

class DataLayerByClassTest extends TestCase
{
    use ProphecyTrait;
    use TestWithInMemoryDatalayer;
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_pick_a_datalayer_for_you()
    {
        $testItem = new DataLayerByClass(
            [
                Order::class => $this->givenAnInMemoryDataLayer(),
            ]
        );
        $otherClass = $this->prophesize(ApieDatalayer::class)->reveal();
        $testItem->setDefaultDataLayer($otherClass);
        $this->assertInstanceOf(InMemoryDatalayer::class, $testItem->pickDataLayerFor(new ReflectionClass(Order::class)));
        $this->assertSame($otherClass, $testItem->pickDataLayerFor(new ReflectionClass(OrderLine::class)));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_becomes_immutable_on_setting_default_datalayer()
    {
        $testItem = new DataLayerByClass([]);
        $testItem->setDefaultDataLayer($this->prophesize(ApieDatalayer::class)->reveal());
        $this->expectException(ObjectIsImmutable::class);
        $testItem[Order::class] = $this->givenAnInMemoryDataLayer();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_not_set_default_datalayer_twice()
    {
        $testItem = new DataLayerByClass([]);
        $testItem->setDefaultDataLayer($this->prophesize(ApieDatalayer::class)->reveal());
        $this->expectException(ObjectIsImmutable::class);
        $testItem->setDefaultDataLayer($this->prophesize(ApieDatalayer::class)->reveal());
    }
}
