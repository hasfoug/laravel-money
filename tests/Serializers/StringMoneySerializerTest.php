<?php

namespace Hasfoug\Money\Tests\Serializers;

use Hasfoug\Money\Money;
use Hasfoug\Money\Serializers\DecimalMoneySerializer;
use Hasfoug\Money\Serializers\IntegerMoneySerializer;
use Hasfoug\Money\Serializers\StringMoneySerializer;
use Hasfoug\Money\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Money\Currency;

class StringMoneySerializerTest extends TestCase
{
    public function testSerializesToInteger()
    {
        $money = Money::USD(100);

        static::assertEquals(
            $money->serialize(new StringMoneySerializer),
            '$1.00'
        );
    }

    public function testSerializesByDefaultToDecimalWhenDefaultSerializerIsDecimal()
    {
        $money = Money::USD(100);

        Config::set('money.defaultSerializer', StringMoneySerializer::class);

        static::assertEquals(
            $money->serialize(),
            '$1.00'
        );
    }
}
