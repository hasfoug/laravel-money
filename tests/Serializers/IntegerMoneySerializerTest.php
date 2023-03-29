<?php

namespace Hasfoug\Money\Tests\Serializers;

use Hasfoug\Money\Money;
use Hasfoug\Money\Serializers\DecimalMoneySerializer;
use Hasfoug\Money\Serializers\IntegerMoneySerializer;
use Hasfoug\Money\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Money\Currency;

class IntegerMoneySerializerTest extends TestCase
{
    public function testSerializesToInteger()
    {
        $money = Money::USD(100);

        static::assertEquals(
            $money->serialize(new IntegerMoneySerializer()),
            100
        );
    }

    public function testSerializesByDefaultToDecimalWhenDefaultSerializerIsDecimal()
    {
        $money = Money::USD(100);

        Config::set('money.defaultSerializer', IntegerMoneySerializer::class);

        static::assertEquals(
            $money->serialize(),
            100
        );
    }
}
