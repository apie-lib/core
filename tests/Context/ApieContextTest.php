<?php
namespace Apie\Tests\Core\Context;

use Apie\Core\Context\ApieContext;
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
}