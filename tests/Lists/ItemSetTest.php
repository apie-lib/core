<?php
namespace Apie\Tests\Core\Lists;

use Apie\Core\Exceptions\IndexNotFoundException;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\Exceptions\ObjectIsImmutable;
use Apie\Core\Lists\ItemSet;
use Apie\Fixtures\Lists\ImmutableStringOrIntList;
use Apie\Fixtures\Lists\ImmutableStringOrIntSet;
use Apie\Fixtures\Lists\StringOrIntList;
use Apie\Fixtures\Lists\StringOrIntSet;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use cebe\openapi\spec\Reference;
use PHPUnit\Framework\TestCase;

class ItemSetTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;
    /**
     * @test
     */
    public function it_has_all_array_functionality()
    {
        $input = [1, 'a', $this];
        $testItem = new ItemSet($input);
        $this->assertEquals($input, $testItem->jsonSerialize());
        $this->assertEquals($input, $testItem->toArray());
        $this->assertCount(3, $testItem);
        $this->assertSame($this, $testItem[$this]);

        $this->assertFalse(isset($testItem[3]));
        $this->assertTrue(isset($testItem['a']));

        $testItem[2] = 'pizza';
        $input[3] = 'pizza';
        $this->assertEquals($input, $testItem->toArray());

        $testItem[] = 'appended';
        $input[] = 'appended';
        $this->assertCount(5, $testItem);
        $this->assertEquals($input, $testItem->toArray());

        unset($testItem[1]);
        $expected = ['a', $this, 'pizza', 'appended'];
        $this->assertEquals($expected, $testItem->toArray());
        $this->assertFalse(isset($testItem[1]));

        unset($testItem['pizza']);
        $expected = ['a', $this, 'appended'];
        $this->assertEquals($expected, $testItem->toArray());
    }

    /**
     * @test
     */
    public function it_can_only_contain_items_once()
    {
        $someClass = new class {
        };
        $clone = clone $someClass;
        $testItem = new ItemSet([1, '1', 1, '1', $someClass, $clone, $someClass, $clone]);
        $this->assertEquals([1, '1', $someClass, $clone], $testItem->toArray());
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
    public function incorrect_index_throws_error()
    {
        $testItem = new ItemSet();
        $this->expectException(IndexNotFoundException::class);
        $this->fail($testItem[0]);
    }

    /**
     * @test
     */
    public function i_can_iterate_with_foreach()
    {
        $count = 0;
        foreach (new ItemSet([1, 2]) as $key => $value) {
            $this->assertSame($count, $key);
            $this->assertSame($count + 1, $value);
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
        new StringOrIntSet([$this]);
    }

    /**
     * @test
     */
    public function it_encodes_json_correctly()
    {
        $testItem = new ItemSet([]);
        $this->assertEquals('[]', json_encode($testItem));
        $testItem = new ItemSet([1, null, 1, 2]);
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
        $testItem = new ImmutableStringOrIntSet([1, 2, 3]);
        $this->expectException(ObjectIsImmutable::class);
        unset($testItem[1]);
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(ItemSet::class);
        $this->runFakerTest(StringOrIntSet::class);
    }

    /**
     * @test
     */
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            ItemSet::class,
            'ItemSet-post',
            [
                'type' => 'array',
                'items' => new Reference(['$ref' => '#/components/schemas/mixed']),
                'uniqueItems' => true,
            ]
        );

        $this->runOpenapiSchemaTestForCreation(
            StringOrIntSet::class,
            'StringOrIntSet-post',
            [
                'type' => 'array',
                'items' => [
                    'oneOf' => [
                        ['type' => 'string'],
                        ['type' => 'integer'],
                    ],
                ],
                'uniqueItems' => true,
            ]
        );
    }
}
