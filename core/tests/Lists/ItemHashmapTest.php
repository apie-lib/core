<?php
namespace Apie\Tests\Core\Lists;

use Apie\Core\Exceptions\IndexNotFoundException;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\Exceptions\ObjectIsImmutable;
use Apie\Core\Lists\ItemHashmap;
use Apie\Fixtures\Lists\ImmutableStringOrIntHashmap;
use Apie\Fixtures\Lists\StringOrIntHashmap;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use cebe\openapi\spec\Reference;
use PHPUnit\Framework\TestCase;
use stdClass;

class ItemHashmapTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;
    /**
     * @test
     */
    public function it_has_all_array_functionality()
    {
        $input = [1, 'a', $this];
        $testItem = new ItemHashmap($input);
        $expected = new stdClass;
        $expected->{"0"} = 1;
        $expected->{"1"} = 'a';
        $expected->{"2"} = $this;
        $this->assertEquals($expected, $testItem->jsonSerialize());
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
        unset($input[1]);
        $this->assertEquals($input, $testItem->toArray());
        $this->assertFalse(isset($testItem[1]));

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
        $testItem = new StringOrIntHashmap($input);
        $expected = new stdClass;
        $expected->{"0"} = 1;
        $expected->{"1"} = 'a';
        $expected->{"2"} = '2';
        $this->assertEquals($expected, $testItem->jsonSerialize());
        $this->assertEquals($input, $testItem->toArray());
        $this->assertCount(3, $testItem);
        $this->assertSame('2', $testItem[2]);

        $testItem[3] = 42;
        $input[3] = 42;
        $this->assertEquals($input, $testItem->toArray());
    }

    /**
     * @test
     */
    public function incorrect_index_throws_error()
    {
        $testItem = new ItemHashmap();
        $this->expectException(IndexNotFoundException::class);
        $this->fail($testItem[0]);
    }

    /**
     * @test
     */
    public function i_can_iterate_with_foreach()
    {
        $count = 0;
        foreach (new ItemHashmap([1, 1]) as $key => $value) {
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
        new StringOrIntHashmap([$this]);
    }

    /**
     * @test
     */
    public function it_encodes_json_correctly()
    {
        $testItem = new ItemHashmap([]);
        $this->assertEquals('{}', json_encode($testItem));
        $testItem = new ItemHashmap([1, null, 2]);
        $this->assertEquals('{"0":1,"1":null,"2":2}', json_encode($testItem));
    }

    /**
     * @test
     */
    public function it_throws_errors_if_hashmap_is_immutable()
    {
        $testItem = new ImmutableStringOrIntHashmap([1, 2, 3]);
        $this->expectException(ObjectIsImmutable::class);
        $testItem[2] = 3;
    }

    /**
     * @test
     */
    public function an_immutable_hashmap_can_not_unset_values()
    {
        $testItem = new ImmutableStringOrIntHashmap([1, 2, 3]);
        $this->expectException(ObjectIsImmutable::class);
        unset($testItem[1]);
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(ItemHashmap::class);
        $this->runFakerTest(StringOrIntHashmap::class);
    }

    /**
     * @test
     */
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            ItemHashmap::class,
            'ItemHashmap-post',
            [
                'type' => 'object',
                'additionalProperties' => new Reference(['$ref' => '#/components/schemas/mixed']),
            ]
        );

        $this->runOpenapiSchemaTestForCreation(
            StringOrIntHashmap::class,
            'StringOrIntHashmap-post',
            [
                'type' => 'object',
                'additionalProperties' => [
                    'oneOf' => [
                        ['type' => 'string'],
                        ['type' => 'integer'],
                    ],
                ],
            ]
        );
    }
}
