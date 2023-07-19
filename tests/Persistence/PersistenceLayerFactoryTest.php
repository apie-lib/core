<?php
namespace Apie\Core\Persistence;

use Apie\Fixtures\BoundedContextFactory;
use PHPUnit\Framework\TestCase;

class PersistenceLayerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_create_a_database_schema_from_multiple_bounded_contexts(): void
    {
        $testItem = new PersistenceLayerFactory(
            PersistenceMetadataFactory::create()
        );
        $actual = $testItem->create(BoundedContextFactory::createHashmapWithMultipleContexts());
        //        var_dump(json_decode(json_encode(Serializer::create()->normalize($actual, new ApieContext())), true));
        $this->assertNotEmpty($actual->toArray());
    }
}
