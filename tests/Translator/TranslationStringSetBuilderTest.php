<?php
namespace Apie\Tests\Core\Translator;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Translator\TranslationStringSetBuilder;
use Apie\Fixtures\Entities\CollectionItemOwned;
use Apie\Fixtures\Entities\ImageFile;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\Polymorphic\Animal;
use Apie\Fixtures\Entities\UserWithAddress;
use Generator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TranslationStringSetBuilderTest extends TestCase
{
    /**
     * @test
     * @dataProvider translationProvider
     */
    public function it_can_provide_a_list_of_all_possible_translations_of_a_class(
        string $expectedFile,
        ReflectionClass $input
    ) {
        $builder = TranslationStringSetBuilder::create($input, new BoundedContextId('test'));
        $actual = json_decode(json_encode($builder->makeAllVariations()), true);
        file_put_contents($expectedFile, json_encode($actual, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
        $expected = json_decode(file_get_contents($expectedFile), true);
        $this->assertEquals($expected, $actual);
    }

    public function translationProvider(): Generator
    {
        $path = __DIR__ . '/../../fixtures/Translations/';
        yield 'simple entity' => [$path . 'user-plain.json', new ReflectionClass(UserWithAddress::class)];
        yield 'with permission checks' => [$path . 'collection-item-owned-plain.json', new ReflectionClass(CollectionItemOwned::class)];
        yield 'with file field' => [$path . 'image-file.json', new ReflectionClass(ImageFile::class)];
        yield 'root aggregate' => [$path . 'order-plain.json', new ReflectionClass(Order::class)];
        yield 'polymorphic' => [$path . 'animal-plain.json', new ReflectionClass(Animal::class)];
    }

}
