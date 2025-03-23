<?php
namespace Apie\Tests\Core\Utils;

use Apie\Core\Utils\EnumUtils;
use Apie\Fixtures\Enums\ColorEnum;
use Apie\Fixtures\Enums\EmptyEnum;
use Apie\Fixtures\Enums\IntEnum;
use Apie\Fixtures\Enums\NoValueEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionEnum;

class EnumUtilsTest extends TestCase
{
    #[Test]
    #[DataProvider('getValuesProvider')]
    public function it_can_show_enums_as_array(array $expected, string $className)
    {
        $this->assertEquals(
            $expected,
            EnumUtils::getValues(new ReflectionEnum($className))
        );
    }

    public static function getValuesProvider(): \Generator
    {
        yield EmptyEnum::class => [[], EmptyEnum::class];
        yield NoValueEnum::class => [['RED' => 'RED', 'GREEN' => 'GREEN', 'BLUE' => 'BLUE'], NoValueEnum::class];
        yield ColorEnum::class => [['red' => 'RED', 'green' => 'GREEN', 'blue' => 'BLUE'], ColorEnum::class];
        yield IntEnum::class => [[0 => 'RED', 1 => 'GREEN', 2 => 'BLUE'], IntEnum::class];
    }

    #[Test]
    #[DataProvider('typesProvider')]
    public function it_can_get_type_of_enum(bool $expectedString, bool $expectedInt, string $className)
    {
        $this->assertEquals(
            $expectedString,
            EnumUtils::isStringEnum(new ReflectionEnum($className))
        );
        $this->assertEquals(
            $expectedInt,
            EnumUtils::isIntEnum(new ReflectionEnum($className))
        );
    }

    public static function typesProvider(): \Generator
    {
        yield EmptyEnum::class => [true, false, EmptyEnum::class];
        yield NoValueEnum::class => [true, false, NoValueEnum::class];
        yield ColorEnum::class => [true, false, ColorEnum::class];
        yield IntEnum::class => [false, true, IntEnum::class];
    }
}
