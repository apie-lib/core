<?php

namespace Apie\Core\ValueObjects;

class Price extends Decimal
{
    public function toCharmPrice(int $ending = 95): self
    {
        if ($ending < 0 || $ending > 99) {
            throw new \InvalidArgumentException('Ending must be between 0 and 99.');
        }

        $integer = $this->integerPart;

        if ((int) $this->decimalPart < $ending) {
            $integer--;
        }

        return self::fromNative(sprintf('%d.%02d', $integer, $ending));
    }

    public static function getNumberOfDecimals(): int
    {
        return 2;
    }
}
