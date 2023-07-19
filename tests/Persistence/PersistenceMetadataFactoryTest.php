<?php
namespace Apie\Tests\Core\Persistence;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Lists\ReflectionClassList;
use Apie\Core\Lists\ReflectionMethodList;
use Apie\Core\Persistence\PersistenceMetadataFactory;
use Apie\Fixtures\Entities\Order;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PersistenceMetadataFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_create_persistence_metadata_for_an_entity()
    {
        $testItem = PersistenceMetadataFactory::create();
        $boundedContext = new BoundedContext(
            new BoundedContextId('domain'),
            new ReflectionClassList(),
            new ReflectionMethodList()
        );
        $actual = $testItem->createEntityMetadata(new ReflectionClass(Order::class), $boundedContext);
        $this->assertEquals('apie_entity_domain_order', $actual->getName());
    }
}
