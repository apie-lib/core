<?php
namespace Apie\Tests\Core\Lists;

use Apie\Core\Exceptions\IndexNotFoundException;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\Exceptions\ObjectIsImmutable;
use Apie\Core\Lists\ItemList;
use Apie\Fixtures\Lists\ImmutableStringOrIntList;
use Apie\Fixtures\Lists\StringOrIntList;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use cebe\openapi\spec\Reference;
use PHPUnit\Framework\TestCase;

class ItemListTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;
    /**
     * @test
     */
    public function it_has_all_array_functionality()
    {
        $input = [1, 'a', $this];
        $testItem = new ItemList($input);
        $this->assertEquals($input, $testItem->jsonSerialize());
        $this->assertEquals($input, $testItem->toArray());
        $this->assertCount(3, $testItem);
        $this->assertSame($this, $testItem[2]);

        $this->assertFalse(isset($testItem[3]));
        $this->assertFalse(isset($testItem['a']));

        $testItem[2] = 'pizza';
        $input[2] = 'pizza';
        $this->assertEquals($input, $testItem->toArray());

        $testItem[] = 'appended';
        $this->assertCount(4, $testItem);

        $input[] = 'appended';
        $this->assertEquals($input, $testItem->toArray());

        unset($testItem[1]);
        $input[1] = null;
        $this->assertEquals($input, $testItem->toArray());
        $this->assertTrue(isset($testItem[1]));

        unset($testItem[3]);
        unset($input[3]);
        $this->assertEquals($input, $testItem->toArray());
    }

    /**
     * @test
     */
    public function if_class_is_extended_it_can_restrict_types()
    {
        $input = [1, 'a', '2'];
        $testItem = new StringOrIntList($input);
        $this->assertEquals($input, $testItem->jsonSerialize());
        $this->assertEquals($input, $testItem->toArray());
        $this->assertCount(3, $testItem);
        $this->assertSame('2', $testItem[2]);

        $testItem[3] = 42;
        $input[3] = 42;
        $this->assertEquals($input, $testItem->toArray());

        $this->expectException(IndexNotFoundException::class);
        $testItem[8] = 10;
    }

    /**
     * @test
     */
    public function if_class_is_extended_it_can_not_remove_items_in_the_middle()
    {
        $input = [1, 'a', '2'];
        $testItem = new StringOrIntList($input);
        $this->expectException(IndexNotFoundException::class);
        unset($testItem[1]);
    }

    /**
     * @test
     */
    public function negative_index_is_wrong()
    {
        $testItem = new ItemList();
        $this->expectException(IndexNotFoundException::class);
        $testItem[-1] = 1;
    }

    /**
     * @test
     */
    public function incorrect_index_throws_error()
    {
        $testItem = new ItemList();
        $this->expectException(IndexNotFoundException::class);
        $this->fail($testItem[0]);
    }

    /**
     * @test
     */
    public function i_can_iterate_with_foreach()
    {
        $count = 0;
        foreach (new ItemList([1, 1]) as $key => $value) {
            $this->assertSame($count, $key);
            $this->assertSame(1, $value);
            $count++;
        }
        $this->assertEquals(2, $count);
    }

    /**
     * @test
     */
    public function if_extended_it_throws_errors_on_wrong_types()
    {
        $this->expectException(InvalidTypeException::class);
        new StringOrIntList([$this]);
    }

    /**
     * @test
     */
    public function it_encodes_json_correctly()
    {
        $testItem = new ItemList([]);
        $this->assertEquals('[]', json_encode($testItem));
        $testItem = new ItemList([1, null, 2]);
        $this->assertEquals('[1,null,2]', json_encode($testItem));
    }

    /**
     * @test
     */
    public function it_throws_errors_if_list_is_immutable()
    {
        $testItem = new ImmutableStringOrIntList([1, 2, 3]);
        $this->expectException(ObjectIsImmutable::class);
        $testItem[2] = 3;
    }

    /**
     * @test
     */
    public function an_immutable_list_can_not_unset_values()
    {
        $testItem = new ImmutableStringOrIntList([1, 2, 3]);
        $this->expectException(ObjectIsImmutable::class);
        unset($testItem[1]);
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(ItemList::class);
        $this->runFakerTest(StringOrIntList::class);
    }

    /**
     * @test
     */
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            ItemList::class,
            'ItemList-post',
            [
                'type' => 'array',
                'items' => new Reference(['$ref' => 'mixed']),
            ]
        );

        $this->runOpenapiSchemaTestForCreation(
            StringOrIntList::class,
            'StringOrIntList-post',
            [
                'type' => 'array',
                'items' => [
                    'oneOf' => [
                        ['type' => 'string'],
                        ['type' => 'integer'],
                    ],
                ],
            ]
        );
    }
}
