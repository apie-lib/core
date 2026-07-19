<?php
namespace Apie\Tests\Core\Translator\ValueObjects;

use Apie\Core\Translator\ValueObjects\TranslationStringPrefix;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TranslationStringPrefixTest extends TestCase
{
    use TestWithFaker;

    #[Test]
    #[DataProvider('validInputProvider')]
    public function it_can_create_object_from_fromNative(string $input)
    {
        $testItem = TranslationStringPrefix::fromNative($input);
        $this->assertEquals($input, $testItem->toNative());
    }

    #[Test]
    #[DataProvider('validTranslationStringsProvider')]
    public function it_can_create_object_from_full_translation_string(string $expected, string $input)
    {
        $testItem = TranslationStringPrefix::createFromTranslation($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public static function validInputProvider(): \Generator
    {
        yield 'none' => ['apie.'];
        yield 'bounded context only' => ['apie.bounded.test.'];
        yield 'bounded context and resource' => ['apie.bounded.test.resource.example.'];
        yield 'resource only' => ['apie.resource.example.'];
    }

    public static function validTranslationStringsProvider(): \Generator
    {
        foreach (self::validInputProvider() as $description => $prefix) {
            yield $description . ', empty string' => [$prefix[0], $prefix[0] . ''];
            yield $description . ', single word' => [$prefix[0], $prefix[0] . 'word'];
            yield $description . ', single word, full suffix' => [$prefix[0], $prefix[0] . 'word.singular.authenticated'];
        }
    }

    #[Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(TranslationStringPrefix::class);
    }
}
