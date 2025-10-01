<?php

namespace Apie\Core\ValueObjects;

class Price extends Decimal
{

    public static function getNumberOfDecimals(): int
    {
        return 2;
    }
}
