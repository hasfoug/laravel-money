<?php
declare(strict_types=1);

namespace Hasfoug\Money\Serializers;

use Hasfoug\Money\Money;
use Hasfoug\Money\Contracts\MoneySerializer;

class StringMoneySerializer implements MoneySerializer
{
    public function serialize(Money $money): string
    {
        return $money->formatByIntl();
    }
}
