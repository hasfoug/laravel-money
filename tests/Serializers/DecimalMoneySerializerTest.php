<?php

namespace Hasfoug\Money\Tests\Serializers;

use Hasfoug\Money\Money;
use Hasfoug\Money\Serializers\DecimalMoneySerializer;
use Hasfoug\Money\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Money\Currency;

class DecimalMoneySerializerTest extends TestCase
{
    public function testSerializesToDecimal()
    {
        $money = Money::USD(100);

        static::assertEquals(
            $money->serialize(new DecimalMoneySerializer),
            1.00
        );
    }

    public function testSerializesByDefaultToDecimalWhenDefaultSerializerIsDecimal()
    {
        $money = Money::USD(100);

        Config::set('money.defaultSerializer', DecimalMoneySerializer::class);

        static::assertEquals(
            $money->serialize(),
            1.00
        );
    }
}
