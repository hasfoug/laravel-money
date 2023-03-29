<?php
declare(strict_types=1);

namespace Hasfoug\Money\Casts;

use Hasfoug\Money\Money;

class MoneyIntegerCast extends MoneyCast
{
    protected function getFormatter(Money $money): int
    {
        return $money->getAmount();
    }
}
