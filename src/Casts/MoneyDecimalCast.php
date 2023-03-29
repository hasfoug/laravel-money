<?php
declare(strict_types=1);

namespace Hasfoug\Money\Casts;

use Hasfoug\Money\Money;

class MoneyDecimalCast extends MoneyCast
{
    protected function getFormatter(Money $money): float
    {
        return (float) $money->formatByDecimal();
    }
}
