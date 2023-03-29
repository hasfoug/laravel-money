<?php
declare(strict_types=1);

namespace Hasfoug\Money\Serializers;

use Hasfoug\Money\Money;
use Hasfoug\Money\Contracts\MoneySerializer;

class IntegerMoneySerializer implements MoneySerializer
{
    public function serialize(Money $money): int
    {
        return $money->getAmount();
    }
}
