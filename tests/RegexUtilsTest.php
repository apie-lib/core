<?php
namespace Apie\Tests\Core;

use Apie\Core\RegexUtils;
use Generator;
use PHPUnit\Framework\TestCase;

class RegexUtilsTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('maxLengthProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_figure_out_maximum_length_of_a_regular_expression(?int $expected, string $input)
    {
        $this->assertEquals($expected, RegexUtils::getMaximumAcceptedStringLengthOfRegularExpression($input));
    }

    public static function maxLengthProvider(): Generator
    {
        yield 'no start and end delimiter' => [null, '/aaa/'];
        yield 'open regex with *' => [null, '/^.*$/'];
        yield 'open regex with +' => [null, '/^.+$/'];
        yield 'static string' => [1, '/^a$/'];
        yield 'end with escaped $' => [null, '/^aa\\$/'];
        yield 'escaped character' => [5, '/^aa\\\\bb$/'];
        yield 'character limits' => [11, '/^a{5,8}b{2,3}$/'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('delimiterProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_remove_delimiters(string $expected, string $input)
    {
        $this->assertEquals($expected, RegexUtils::removeDelimiters($input));
    }

    public static function delimiterProvider(): \Generator
    {
        yield 'no start and end delimiter' => ['aaa', '/aaa/'];
        yield 'different delimiter' => ['aaa', 'baaab'];
        yield 'case insensitive' => ['(a|A)(a|A)(a|A)', '/aaa/i'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('translationStringProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_replace_translation_strings_to_regex(
        string $expected,
        string $input,
        bool $includeStartAndEndRegex,
        bool $includeAsCaptureGroup
    ) {
        $this->assertEquals(
            $expected,
            RegexUtils::fromPlaceholderToRegularExpression(
                $input,
                $includeStartAndEndRegex,
                $includeAsCaptureGroup
            )
        );
    }

    public static function translationStringProvider(): \Generator
    {
        yield 'no translation strings' => [
            'apie.menu.header.authenticated',
            'apie.menu.header.authenticated',
            false,
            false
        ];
        yield 'no translation strings, add start and end regex' => [
            '/^apie.menu.header.authenticated$/',
            'apie.menu.header.authenticated',
            true,
            false
        ];
        yield 'simple placeholder' => [
            'apie.resource.edit.[^.]+.label',
            'apie.resource.edit.:id.label',
            false,
            false
        ];
        yield 'simple placeholder, add start and end regex' => [
            '/^apie.resource.edit.[^.]+.label$/',
            'apie.resource.edit.:id.label',
            true,
            false
        ];
        yield 'simple placeholder, add start and end regex and capture group' => [
            '/^apie.resource.edit.(?<id>[^.]+).label$/',
            'apie.resource.edit.:id.label',
            true,
            true
        ];
        yield 'simple placeholder, add capture group' => [
            'apie.resource.edit.(?<id>[^.]+).label',
            'apie.resource.edit.:id.label',
            false,
            true
        ];
    }
}
