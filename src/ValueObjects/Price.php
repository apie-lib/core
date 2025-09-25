<?php

namespace Apie\Core\ValueObjects;

class Price extends Decimal
{

    static public function getNumberOfDecimals(): int
    {
        return 2;
    }
}