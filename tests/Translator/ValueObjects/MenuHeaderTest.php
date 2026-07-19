<?php
namespace Apie\Tests\Core\Translator\ValueObjects;

use Apie\Core\Translator\ValueObjects\MenuHeader;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MenuHeaderTest extends TestCase
{
    use TestWithFaker;

    #[Test]
    #[DataProvider('validInputProvider')]
    public function it_can_create_object_from_fromNative(string $input)
    {
        $testItem = MenuHeader::fromNative($input);
        $this->assertEquals($input, $testItem->toNative());
    }

    public static function validInputProvider(): \Generator
    {
        yield 'menu header root' => ['apie.menu.header'];
        yield 'submenu item' => ['apie.menu.sub.sub2.header'];
        yield 'all options' => ['apie.bounded.test.resource.test.menu.sub.sub2.header.singular.authenticated'];
    }

    #[Test]
    #[DataProvider('invalidInputProvider')]
    public function fromNative_throws_error_on_invalid_input(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        $testItem = MenuHeader::fromNative($input);
    }

    public static function invalidInputProvider(): \Generator
    {
        yield 'no prefix' => ['menu.header'];
        yield 'double dots' => ['apie.bounded.test..singular.authenticated'];
        yield 'double dots submenu' => ['apie.bounded.menu.test.test..test.header.singular.authenticated'];
        yield 'spaces in middle section' => ['apie.bounded.menu.test.test toevoegen.header.singular.authenticated'];
    }

    #[Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(MenuHeader::class);
    }
}
