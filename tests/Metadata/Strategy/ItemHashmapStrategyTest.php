<?php
namespace Apie\Tests\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\StringHashmap;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\ScalarMetadata;
use Apie\Core\Metadata\Strategy\ItemHashmapStrategy;
use Apie\Core\Metadata\UnionTypeMetadata;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ItemHashmapStrategyTest extends TestCase
{
    /**
     * @test
     * @dataProvider hashmapOptionsProvider
     */
    public function it_can_extract_item_types(string $expectedMetadataClass, string $class)
    {
        $context = new ApieContext();
        /** @var ItemHashmapStrategy $actual */
        $actual = MetadataFactory::getMetadataStrategy(new ReflectionClass($class));
        $this->assertInstanceOf(ItemHashmapStrategy::class, $actual);
        $metadata = $actual->getCreationMetadata($context);
        $this->assertInstanceOf($expectedMetadataClass, $metadata->getArrayItemType());
        $metadata = $actual->getModificationMetadata($context);
        $this->assertInstanceOf($expectedMetadataClass, $metadata->getArrayItemType());
        $metadata = $actual->getResultMetadata($context);
        $this->assertInstanceOf($expectedMetadataClass, $metadata->getArrayItemType());
    }

    public function hashmapOptionsProvider()
    {
        yield [
            ScalarMetadata::class,
            ItemHashmap::class,
        ];
        yield [
            ScalarMetadata::class,
            StringHashmap::class,
        ];
        yield [
            UnionTypeMetadata::class,
            ReflectionHashmap::class,
        ];
    }
}
