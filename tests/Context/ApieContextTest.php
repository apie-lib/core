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
    /**
     * @test
     */
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

    /**
     * @test
     */
    public function it_can_read_context_attributes()
    {
        $testItem = new ApieContext(['test' => false]);
        $refl = new ReflectionClass(ClassWithAttributes::class);
        $this->assertTrue($testItem->appliesToContext($refl->getProperty('property')));
    }

    /**
     * @test
     */
    public function it_throws_an_error_if_some_context_is_not_found()
    {
        $testItem = new ApieContext();
        $this->expectException(IndexNotFoundException::class);
        $testItem->getContext('missing');
    }

    /**
     * @test
     */
    public function it_can_register_services_as_context()
    {
        $testItem = new ApieContext();
        $testItem = $testItem->registerInstance($this);
        $actual = $testItem->getContext(TestCase::class);
        $this->assertSame($this, $actual);
        $testItem = $testItem->registerInstance(new class extends TestCase {
        });
        $testItem = $testItem->registerInstance(new class extends TestCase {
        });
        $actual = $testItem->getContext(TestCase::class);
        $this->assertInstanceOf(AmbiguousCall::class, $actual);
        $this->expectException(AmbiguousCallException::class);
        $actual->it_can_read_context_attributes();
    }
}
