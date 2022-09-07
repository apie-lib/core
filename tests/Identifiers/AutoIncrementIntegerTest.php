<?php
namespace Apie\Tests\Core\Identifiers;

use Apie\Core\Identifiers\AutoIncrementInteger;
use Apie\Fixtures\Identifiers\UserAutoincrementIdentifier;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Faker\Factory;
use PHPUnit\Framework\TestCase;

class AutoIncrementIntegerTest extends TestCase
{
    use TestWithFaker;

    /**
     * @test
     */
    public function it_uses_integers(): void
    {
        $testItem = AutoIncrementInteger::fromNative(null);
        $this->assertNull($testItem->toNative());

        $testItem = AutoIncrementInteger::fromNative(12);
        $this->assertEquals(12, $testItem->toNative());

        $testItem = AutoIncrementInteger::fromNative(13);
        $this->assertEquals(13, $testItem->toNative());
    }

    /**
     * @test
     */
    public function createRandom_autoincrements_by_one_per_class_per_generator(): void
    {
        $generator1 = Factory::create();
        $this->assertEquals(1, AutoIncrementInteger::createRandom($generator1)->toNative());
        $this->assertEquals(2, AutoIncrementInteger::createRandom($generator1)->toNative());
        $this->assertEquals(1, UserAutoincrementIdentifier::createRandom($generator1)->toNative());
        $this->assertEquals(2, UserAutoincrementIdentifier::createRandom($generator1)->toNative());
        $this->assertEquals(3, AutoIncrementInteger::createRandom($generator1)->toNative());
        $generator2 = Factory::create();
        $this->assertEquals(1, AutoIncrementInteger::createRandom($generator2)->toNative());
        $this->assertEquals(3, UserAutoincrementIdentifier::createRandom($generator1)->toNative());
        $this->assertEquals(1, AutoIncrementInteger::createRandom($generator1)->toNative());
    }

    /**
     * @test
     */
    public function it_works_with_fake_library()
    {
        $this->runFakerTest(AutoIncrementInteger::class);
    }
}
