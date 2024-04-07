<?php
namespace Apie\Tests\Core;

use Apie\Core\RegexUtils;
use Generator;
use PHPUnit\Framework\TestCase;

class RegexUtilsTest extends TestCase
{
    /**
     * @test
     * @dataProvider maxLengthProvider
     */
    public function it_can_figure_out_maximum_length_of_a_regular_expression(?int $expected, string $input)
    {
        $this->assertEquals($expected, RegexUtils::getMaximumAcceptedStringLengthOfRegularExpression($input));
    }

    public function maxLengthProvider(): Generator
    {
        yield 'no start and end delimiter' => [null, '/aaa/'];
        yield 'open regex with *' => [null, '/^.*$/'];
        yield 'open regex with +' => [null, '/^.+$/'];
        yield 'static string' => [1, '/^a$/'];
        yield 'end with escaped $' => [null, '/^aa\\$/'];
        yield 'escaped character' => [5, '/^aa\\\\bb$/'];
        yield 'character limits' => [11, '/^a{5,8}b{2,3}$/'];
    }
}
