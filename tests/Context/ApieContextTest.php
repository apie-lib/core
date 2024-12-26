<?php
namespace Apie\Tests\Core\Context;

use Apie\Core\Context\AmbiguousCall;
use Apie\Core\Context\ApieContext;
use Apie\Core\Exceptions\AmbiguousCallException;
use Apie\Core\Exceptions\IndexNotFoundException;
use Apie\Fixtures\Other\ClassWithAttributes;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ApieContextTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_is_a_immutable_object()
    {
        $testItem = new ApieContext(['test' => 1]);
        $testItem2 = $testItem->withContext('test', 3)->withContext('test2', 5);
        $this->assertEquals(1, $testItem->getContext('test'));
        $this->assertEquals(3, $testItem2->getContext('test'));
        $this->assertEquals(5, $testItem2->getContext('test2'));
        $this->assertFalse($testItem->hasContext('test2'));
        $this->assertTrue($testItem2->hasContext('test2'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_read_context_attributes()
    {
        $testItem = new ApieContext(['test' => false]);
        $refl = new ReflectionClass(ClassWithAttributes::class);
        $this->assertTrue($testItem->appliesToContext($refl->getProperty('property')));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_an_error_if_some_context_is_not_found()
    {
        $testItem = new ApieContext();
        $this->expectException(IndexNotFoundException::class);
        $testItem->getContext('missing');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_register_services_as_context()
    {
        $testItem = new ApieContext();
        $testItem = $testItem->registerInstance($this);
        $actual = $testItem->getContext(TestCase::class);
        $this->assertSame($this, $actual);
        $testItem = $testItem->registerInstance(new class('test') extends TestCase {
        });
        $testItem = $testItem->registerInstance(new class('test2') extends TestCase {
        });
        $actual = $testItem->getContext(TestCase::class);
        $this->assertInstanceOf(AmbiguousCall::class, $actual);
        $this->expectException(AmbiguousCallException::class);
        $actual->it_can_read_context_attributes();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('filterProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_methods_and_properties_with_this_context(array $expectedKeys, array $input)
    {
        $testItem = new ApieContext($input);
        $refl = new ReflectionClass(ClassWithAttributes::class);
        $keys = array_keys($testItem->getApplicableGetters($refl)->toArray());
        $this->assertEquals($expectedKeys, $keys);
    }

    public static function filterProvider()
    {
        yield [
            ['property3'],
            []
        ];
        yield [
            ['property'],
            ['test'=> 1],
        ];
        yield [
            ['property', 'property2'],
            ['test' => 1, 'test2' => 2],
        ];
        yield [
            ['property3'],
            ['test2'=> 1],
        ];
    }
}
