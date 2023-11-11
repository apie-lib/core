<?php
namespace Apie\Tests\Core\Utils;

use Apie\Core\Utils\ValueObjectUtils;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use Apie\CountryAndPhoneNumber\DutchPhoneNumber;
use Generator;
use PHPUnit\Framework\TestCase;

class ValueObjectUtilsTest extends TestCase
{
    /**
     * @dataProvider nonCompositeValueObjectProvider
     */
    public function testIsNonCompositeValueObject(bool $expected, mixed $input): void
    {
        $this->assertEquals(
            $expected,
            ValueObjectUtils::isNonCompositeValueObject($input)
        );
    }

    public function nonCompositeValueObjectProvider(): Generator
    {
        yield [
            true,
            UrlRouteDefinition::class
        ];
        if (class_exists(DutchPhoneNumber::class)) {
            yield [
                true,
                DutchPhoneNumber::class
            ];
        }
    }
}
