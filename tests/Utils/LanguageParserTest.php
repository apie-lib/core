<?php
namespace Apie\Tests\Core\Utils;

use Apie\Core\Utils\LanguageParser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LanguageParserTest extends TestCase
{
    #[Test]
    public function it_can_parse_language_header()
    {
        $input = 'da, en-gb;q=0.8, en;q=0.7';
        $actual = LanguageParser::parseLanguageHeader($input);
        $this->assertEquals(['da', 'en-gb', 'en'], $actual);
    }

    #[Test]
    public function it_handles_quality_values_out_of_order()
    {
        $input = 'en;q=0.7, da;q=1.0, en-gb;q=0.8';
        $actual = LanguageParser::parseLanguageHeader($input);
        $this->assertEquals(['da', 'en-gb', 'en'], $actual);
    }

    #[Test]
    public function it_handles_empty_input()
    {
        $input = '';
        $actual = LanguageParser::parseLanguageHeader($input);
        $this->assertEquals([], $actual);
    }

    #[Test]
    public function it_handles_invalid_format_gracefully()
    {
        $input = 'da;q=invalid, en';
        $actual = LanguageParser::parseLanguageHeader($input);
        $this->assertEquals(['en', 'da'], $actual); // 'da' becomes 0.0 priority if (float)'invalid' is 0.0
    }
}
