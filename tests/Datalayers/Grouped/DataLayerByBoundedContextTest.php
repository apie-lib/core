<?php
namespace Apie\Tests\Core\DataLayers\Grouped;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Core\Datalayers\Grouped\DataLayerByBoundedContext;
use Apie\Core\Datalayers\Grouped\DataLayerByClass;
use Apie\Core\Datalayers\InMemory\InMemoryDatalayer;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\OrderLine;
use Apie\Fixtures\TestHelpers\TestWithInMemoryDatalayer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionClass;

class DataLayerByBoundedContextTest extends TestCase
{
    use ProphecyTrait;
    use TestWithInMemoryDatalayer;

    /**
     * @test
     */
    public function it_can_pick_a_datalayer_for_you()
    {
        $datalayerByClass = new DataLayerByClass(
            [
                Order::class => $this->givenAnInMemoryDataLayer(),
            ]
        );
        $otherClass = $this->prophesize(ApieDatalayer::class)->reveal();
        $datalayerByClass->setDefaultDataLayer($otherClass);

        $testItem = new DataLayerByBoundedContext([
            'default' => $datalayerByClass,
        ]);
        $anotherClass = $this->prophesize(ApieDatalayer::class)->reveal();
        $testItem->setDefaultDataLayer($anotherClass);

        $this->assertInstanceOf(
            InMemoryDatalayer::class,
            $testItem->pickDataLayerFor(
                new ReflectionClass(Order::class),
                new BoundedContextId('default')
            )
        );
        $this->assertSame(
            $otherClass,
            $testItem->pickDataLayerFor(
                new ReflectionClass(OrderLine::class),
                new BoundedContextId('default')
            )
        );
        $this->assertSame(
            $anotherClass,
            $testItem->pickDataLayerFor(
                new ReflectionClass(Order::class),
                new BoundedContextId('incorrect')
            )
        );
    }
}
