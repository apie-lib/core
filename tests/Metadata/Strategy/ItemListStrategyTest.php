<?php
namespace Apie\Tests\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemList;
use Apie\Core\Lists\StringList;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\ScalarMetadata;
use Apie\Core\Metadata\Strategy\ItemListObjectStrategy;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ItemListStrategyTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('hashmapOptionsProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_extract_item_types(string $expectedMetadataClass, string $class)
    {
        $context = new ApieContext();
        /** @var ItemListObjectStrategy $actual */
        $actual = MetadataFactory::getMetadataStrategy(new ReflectionClass($class));
        $this->assertInstanceOf(ItemListObjectStrategy::class, $actual);
        $metadata = $actual->getCreationMetadata($context);
        $this->assertInstanceOf($expectedMetadataClass, $metadata->getArrayItemType());
        $metadata = $actual->getModificationMetadata($context);
        $this->assertInstanceOf($expectedMetadataClass, $metadata->getArrayItemType());
        $metadata = $actual->getResultMetadata($context);
        $this->assertInstanceOf($expectedMetadataClass, $metadata->getArrayItemType());
    }

    public static function hashmapOptionsProvider()
    {
        yield [
            ScalarMetadata::class,
            ItemList::class,
        ];
        yield [
            ScalarMetadata::class,
            StringList::class,
        ];
    }
}
