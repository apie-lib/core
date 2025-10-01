<?php

namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Price;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    #[Test]
    public function fromNative_with_numeric_creates_expected_price(): void
    {
        $price = Price::fromNative(12.345);
        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame('12.35', (string) $price);
        $this->assertSame('12.35', $price->toNative());
        $this->assertSame('"12.35"', json_encode($price, JSON_THROW_ON_ERROR));
    }

    #[Test]
    public function fromNative_with_string_creates_expected_price(): void
    {
        $price = Price::fromNative('42.5');
        $this->assertSame('42.50', (string) $price);
    }

    #[Test]
    public function fromNative_throws_exception_on_invalid_string(): void
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        Price::fromNative('not-a-price');
    }

    #[Test]
    public function getRegularExpression_matches_valid_values(): void
    {
        $regex = Price::getRegularExpression();
        $this->assertMatchesRegularExpression($regex, '0.00');
        $this->assertMatchesRegularExpression($regex, '12.34');
        $this->assertMatchesRegularExpression($regex, '-5.25');
        $this->assertDoesNotMatchRegularExpression($regex, '12');
        $this->assertDoesNotMatchRegularExpression($regex, '12.345');
        $this->assertDoesNotMatchRegularExpression($regex, 'abc');
    }

    #[Test]
    public function it_can_add_multiple_prices(): void
    {
        $a = Price::fromNative(10.00);
        $b = Price::fromNative(5.25);

        $result = $a->add($b, 2, 1.75);
        $this->assertInstanceOf(Price::class, $result);
        $this->assertSame('19.00', (string) $result);
    }

    #[Test]
    public function it_can_subtract_multiple_prices(): void
    {
        $a = Price::fromNative(20.00);
        $b = Price::fromNative(5.25);

        $result = $a->subtract($b, 2.75);
        $this->assertSame('12.00', (string) $result);
    }

    #[Test]
    public function it_can_multiple_multiple_prices(): void
    {
        $a = Price::fromNative(10.00);

        $result = $a->multiply(2, 1.5);
        $this->assertSame('30.00', (string) $result);
    }

    #[Test]
    public function it_can_divide_multiple_prices(): void
    {
        $a = Price::fromNative(10.00);

        $result = $a->divide(2);
        $this->assertSame('5.00', (string) $result);
    }

    #[Test]
    public function it_can_throw_division_by_zero(): void
    {
        $a = Price::fromNative(10.00);

        $this->expectException(\DivisionByZeroError::class);
        $a->divide(0);
    }
}
