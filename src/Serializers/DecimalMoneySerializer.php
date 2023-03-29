<?php
declare(strict_types=1);

namespace Hasfoug\Money\Serializers;

use Hasfoug\Money\Contracts\MoneySerializer;
use Hasfoug\Money\Money;

class DecimalMoneySerializer implements MoneySerializer
{
    public function serialize(Money $money): float
    {
        return (float) $money->formatByDecimal();
    }
}
