<?php
namespace Apie\Tests\Core\Translator\ValueObjects;

use Apie\Core\Translator\ValueObjects\ResourceModifyResourceButtonLabel;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ResourceModifyResourceButtonLabelTest extends TestCase
{
    use TestWithFaker;

    #[Test]
    #[DataProvider('validInputProvider')]
    public function it_can_create_object_from_fromNative(
        array $expectedPlaceholders,
        string $expectedFallback,
        string $input
    ) {
        $testItem = ResourceModifyResourceButtonLabel::fromNative($input);
        $this->assertEquals($input, $testItem->toNative());
        $this->assertEquals($expectedFallback, $testItem->getFallbackText());
        $this->assertEquals($expectedPlaceholders, $testItem->getPlaceholders()->toArray());
    }

    public static function validInputProvider(): \Generator
    {
        yield 'simple label' => [['id' => '12345678'], 'Edit', 'apie.action.edit.12345678.label'];
    }

    #[Test]
    #[DataProvider('invalidInputProvider')]
    public function fromNative_throws_error_on_invalid_input(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        ResourceModifyResourceButtonLabel::fromNative($input);
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
        $this->runFakerTest(ResourceModifyResourceButtonLabel::class);
    }
}
