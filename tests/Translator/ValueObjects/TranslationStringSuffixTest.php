<?php
namespace Apie\Tests\Core\Translator\ValueObjects;

use Apie\Core\Translator\ValueObjects\TranslationStringSuffix;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TranslationStringSuffixTest extends TestCase
{
    use TestWithFaker;

    #[Test]
    #[DataProvider('validInputProvider')]
    public function it_can_create_object_from_fromNative(string $input)
    {
        $testItem = TranslationStringSuffix::fromNative($input);
        $this->assertEquals($input, $testItem->toNative());
        $this->assertEquals($input, $testItem->jsonSerialize());
    }

    #[Test]
    #[DataProvider('validTranslationStringsProvider')]
    public function it_can_create_object_from_full_translation_string(string $expected, string $input)
    {
        $testItem = TranslationStringSuffix::createFromTranslation($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public static function validInputProvider(): \Generator
    {
        yield 'none' => [''];
        yield 'singular' => ['.singular'];
        yield 'singular, authenticated' => ['.singular.authenticated'];
        yield 'singular, unauthenticated' => ['.singular.unauthenticated'];
        yield 'plural' => ['.plural'];
        yield 'plural, authenticated' => ['.plural.authenticated'];
        yield 'plural, unauthenticated' => ['.plural.unauthenticated'];
        yield 'authenticated' => ['.authenticated'];
        yield 'unauthenticated' => ['.unauthenticated'];
    }

    public static function validTranslationStringsProvider(): \Generator
    {
        foreach (self::validInputProvider() as $description => $suffix) {
            yield $description . ', empty string' => [$suffix[0], $suffix[0]];
            yield $description . ', single word' => [$suffix[0], 'word' . $suffix[0]];
            yield $description . ', single word, full suffix' => [$suffix[0], 'apie.bounded.example.resource.test.word' . $suffix[0]];
        }
    }

    #[Test]
    #[DataProvider('simplificationsProvider')]
    public function it_can_provide_translation_simplifications(array $expected, string $input)
    {
        $testItem = TranslationStringSuffix::fromNative($input);
        $this->assertEquals(
            $expected,
            json_decode(json_encode($testItem->getSimplifications()), true)
        );
    }

    public static function simplificationsProvider(): \Generator
    {
        yield 'none' => [[], ''];
        yield 'singular' => [[''], '.singular'];
        yield 'singular, authenticated' => [['.authenticated', '.singular'], '.singular.authenticated'];
        yield 'singular, unauthenticated' => [['.unauthenticated', '.singular'], '.singular.unauthenticated'];
        yield 'plural' => [[''], '.plural'];
        yield 'plural, authenticated' => [['.authenticated', '.plural'], '.plural.authenticated'];
        yield 'plural, unauthenticated' => [['.unauthenticated', '.plural'], '.plural.unauthenticated'];
        yield 'authenticated' => [[''], '.authenticated'];
        yield 'unauthenticated' => [[''], '.unauthenticated'];
    }

    #[Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(TranslationStringSuffix::class);
    }
}
