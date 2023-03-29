<?php
declare(strict_types=1);

namespace Hasfoug\Money\Contracts;

use Hasfoug\Money\Money;

interface MoneySerializer
{
    public function serialize(Money $money): mixed;
}
