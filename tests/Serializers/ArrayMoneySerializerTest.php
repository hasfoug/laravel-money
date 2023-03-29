<?php

namespace Hasfoug\Money\Tests\Serializers;

use Hasfoug\Money\Money;
use Hasfoug\Money\Tests\TestCase;
use Money\Currency;

class ArrayMoneySerializerTest extends TestCase
{
    public function testSerializesToArray()
    {
        $money = Money::USD(100);

        static::assertEquals(
            $money->jsonSerialize(),
            ['amount' => '100', 'currency' => 'USD', 'formatted' => '$1.00']
        );
    }
}
