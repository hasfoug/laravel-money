<?php
declare(strict_types=1);

namespace Hasfoug\Money\Serializers;

use Hasfoug\Money\Contracts\MoneySerializer;
use Hasfoug\Money\Money;

class ArrayMoneySerializer implements MoneySerializer
{
    public function serialize(Money $money): array
    {
        return array_merge(
            $money->getAttributes(),
            $money->getMoney()->jsonSerialize(),
            ['formatted' => $money->render()]
        );
    }
}
