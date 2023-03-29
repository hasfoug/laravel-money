<?php
declare(strict_types=1);

namespace Hasfoug\Money\Casts;

use Hasfoug\Money\Money;

class MoneyStringCast extends MoneyCast
{
    protected function getFormatter(Money $money): string
    {
        return $money->formatByIntl();
    }
}
