# Laravel Money

[![License](https://poser.pugx.org/cknow/laravel-money/license)](https://packagist.org/packages/cknow/laravel-money)

> **Note:** This project is based on [cknow/laravel-money](https://github.com/cknow/laravel-money) and abstracts [MoneyPHP](http://moneyphp.org/)

## Installation

Run the following command from you terminal:

```bash
composer require hasfoug/laravel-money
```

## Usage

```php
use Hasfoug\Money\Money;

echo Money::USD(500); // $5.00
echo Money::USD(500, true); // $500.00 force decimals
```

## Configuration

The defaults are set in `config/money.php`. Copy this file to your own config directory to modify the values. You can publish the config using this command:

```bash
php artisan vendor:publish --provider="Hasfoug\Money\MoneyServiceProvider"
```

This is the contents of the published file:

```php
return [
    /*
     |--------------------------------------------------------------------------
     | Laravel money
     |--------------------------------------------------------------------------
     */
    'locale' => config('app.locale', 'en_US'),
    'defaultCurrency' => config('app.currency', 'USD'),
    'defaultFormatter' => null,
    'defaultSerializer' => null,
    'currencies' => [
        'iso' => ['RUB', 'USD', 'EUR'],  // 'all' to choose all ISOCurrencies
        'bitcoin' => ['XBT'], // 'all' to choose all BitcoinCurrencies
        'custom' => [
            'MY1' => 2,
            'MY2' => 3
        ]
    ]
];
```

## Main changes to the cknow/laravel-money version:

### Most operations now accept mixed values, parsing them to default currency before operation
Therefore, you are not required need to convert the operands to money instance anymore (unless you operate in non-default currency):

```php
Money::USD(500)->add(100); // $6.00
Money::USD(500)->add(null); // $5.00
Money::USD(500)->subtract(100); // $4.00
Money::USD(500)->subtract(null); // $5.00

// Aggregation
Money::min(Money::USD(100), Money::USD(200), 300, null); // Money::USD(0)
Money::max(Money::USD(100), Money::USD(200), 400); // Money::USD(400)
Money::avg(Money::USD(100), Money::USD(200), 300); // Money::USD(200)
Money::sum(Money::USD(100), Money::USD(200), 300, null, 0); // Money::USD(600)

// Comparing
Money::USD(500)->equals(500); // true
Money::USD(500)->greaterThan(100); // true
Money::USD(500)->greaterThan(null); // true
Money::USD(500)->greaterThanOrEqual(500); // true
Money::USD(500)->lessThan(1000); // true
Money::USD(500)->lessThan(null); // false
Money::USD(500)->lessThanOrEqual(500); // true
Money::USD(0)->lessThanOrEqual(null); // true
```

### Added shortcuts for comparing operations

```php
Money::USD(500)->eq(500); // true
Money::USD(500)->gt(100); // true
Money::USD(500)->gte(500); // true
Money::USD(500)->lt(1000); // true
Money::USD(500)->lte(500); // true
```

### Added isNotZero accessor

```php
Money::USD(500)->isNotZero(); // true
Money::USD(0)->isNotZero(); // false
```

### getAmount() method return type switched to int

```php
Money::USD(500)->getAmount(); // '500' -> Before
Money::USD(500)->isNotZero(); // 500 -> Now
```

### Added defaultSerializer to the config to set default json serialization behaviour

Change the value of money.defaultSerializer setting to one of those values:
- \Hasfoug\Money\Tests\Serializers\ArrayMoneySerializer::class
- \Hasfoug\Money\Tests\Serializers\DecimalMoneySerializer::class
- \Hasfoug\Money\Tests\Serializers\IntegerMoneySerializer::class
- \Hasfoug\Money\Tests\Serializers\StringMoneySerializer::class

Or create your own, which should implement \Hasfoug\Money\Contracts\MoneySerializer

## Advanced Usage

> See [MoneyPHP](http://moneyphp.org/) for more information

```php
use Hasfoug\Money\Money;

Money::USD(500)->add(Money::USD(500)); // $10.00
Money::USD(500)->add(Money::USD(500), Money::USD(500)); // $15.00
Money::USD(500)->subtract(Money::USD(400)); // $1.00
Money::USD(500)->subtract(Money::USD(200), Money::USD(100)); // $2.00
Money::USD(500)->multiply(2); // $10.00
Money::USD(1000)->divide(2); // $5.00
Money::USD(830)->mod(Money::USD(300)); // $2.30 -> Money::USD(230)
Money::USD(-500)->absolute(); // $5.00
Money::USD(500)->negative(); // $-5.00
Money::USD(30)->ratioOf(Money::USD(2)); // 15
Money::USD(500)->isSameCurrency(Money::USD(100)); // true
Money::USD(500)->equals(Money::USD(500)); // true
Money::USD(500)->greaterThan(Money::USD(100)); // true
Money::USD(500)->greaterThanOrEqual(Money::USD(500)); // true
Money::USD(500)->lessThan(Money::USD(1000)); // true
Money::USD(500)->lessThanOrEqual(Money::USD(500)); // true
Money::USD(500)->isZero(); // false
Money::USD(500)->isPositive(); // true
Money::USD(500)->isNegative(); // false
Money::USD(500)->getMoney(); // Instance of \Money\Money
Money::isValidCurrency('USD'); // true
Money::isValidCurrency('FAIL'); // false
Money::getISOCurrencies(); // Load ISO currencies

// Aggregation
Money::min(Money::USD(100), Money::USD(200), Money::USD(300)); // Money::USD(100)
Money::max(Money::USD(100), Money::USD(200), Money::USD(300)); // Money::USD(300)
Money::avg(Money::USD(100), Money::USD(200), Money::USD(300)); // Money::USD(200)
Money::sum(Money::USD(100), Money::USD(200), Money::USD(300)); // Money::USD(600)

// Formatters
Money::USD(500)->format(); // $5.00
Money::USD(199)->format(null, null, \NumberFormatter::DECIMAL); // 1,99
Money::XBT(41000000)->formatByBitcoin(); // \xC9\x830.41
Money::USD(500)->formatByCurrencySymbol(); // $5.00
Money::USD(500)->formatByCurrencySymbol(true); // 5.00$
Money::USD(500)->formatByDecimal(); // 5.00
Money::USD(500)->formatByIntl(); // $5.00
Money::USD(199)->formatByIntl(null, null, \NumberFormatter::DECIMAL); // 1,99
Money::USD(500)->formatByIntlLocalizedDecimal(); // $5.00
Money::USD(199)->formatByIntlLocalizedDecimal(null, null, \NumberFormatter::DECIMAL) // 1.99

// Parsers
Money::parse('$1.00'); // Money::USD(100)
Money::parseByBitcoin("\xC9\x830.41"); // Money::XBT(41000000)
Money::parseByDecimal('1.00', 'USD'); // Money::USD(100)
Money::parseByIntl('$1.00'); // Money::USD(100)
Money::parseByIntlLocalizedDecimal('1.00', 'USD'); // Money::USD(100)
```

### Create your formatter

```php
class MyFormatter implements \Money\MoneyFormatter
{
    public function format(\Money\Money $money)
    {
        return 'My Formatter';
    }
}

Money::USD(500)->formatByFormatter(new MyFormatter()); // My Formatter
```

## Rules

Below is a list of all available validation rules and their function:

### currency

The field under validation must be a valid currency.

```php
Validator::make([
  'currency1' => 'USD',
  'currency2' => 'EUR',
  'currency3' => new \Money\Currency('BRL'),
], [
  'currency1' => new \Hasfoug\Money\Rules\Currency(),
  'currency2' => new \Hasfoug\Money\Rules\Currency(),
  'currency3' => 'currency',
]);
```

### money

The field under validation must be a valid money.

```php
Validator::make([
  'money1' => '$10.00'
  'money2' => '€10.00',
  'money3' => 'R$10,00',
  'money4' => '$10.00'
  'money5' => '€10.00',
  'money6' => 'R$10,00',
], [
  'money1' => new \Hasfoug\Money\Rules\Money(),
  'money2' => new \Hasfoug\Money\Rules\Money('EUR'), // forcing currency
  'money3' => new \Hasfoug\Money\Rules\Money('BRL', 'pt_BR'), // forcing currency and locale
  'money4' => 'money',
  'money5' => 'money:EUR', // forcing currency
  'money6' => 'money:BRL,pt_BR', // forcing currency and locale
]);
```

## Casts

At this stage the cast can be defined in the following ways:

```php
use Hasfoug\Money\Casts\MoneyDecimalCast;
use Hasfoug\Money\Casts\MoneyIntegerCast;
use Hasfoug\Money\Casts\MoneyStringCast;

protected $casts = [
    // cast money as decimal using the currency defined in the package config
    'money' => MoneyDecimalCast::class,
    // cast money as integer using the defined currency
    'money' => MoneyIntegerCast::class . ':AUD',
    // cast money as string using the currency defined in the model attribute 'currency'
    'money' => MoneyStringCast::class . ':currency',
    // cast money as decimal using the defined currency and forcing decimals
    'money' => MoneyDecimalCast::class . ':USD,true',
];
```

In the example above, if the model attribute `currency` is `null`,
the currency defined in the package configuration is used instead.

Setting money can be done in several ways:

```php
$model->money = 10; // 0.10 USD or any other currency defined
$model->money = 10.23; // 10.23 USD or any other currency defined
$model->money = 'A$10'; // 10.00 AUD
$model->money = '1,000.23'; // 1000.23 USD or any other currency defined
$model->money = '10'; // 0.10 USD or any other currency defined
$model->money = Money::EUR(10); // 10 EUR
```

When we pass the model attribute holding the currency,
such attribute is updated as well when setting money:

```php
$model->currency; // null
$model->money = '€13';
$model->currency; // 'EUR'
$model->money->getAmount(); // '1300'
```

## Helpers

```php
currency() // To use default currency present in `config/money.php`
currency('USD');
money(500); // To use default currency present in `config/money.php`
money(500, 'USD');

// Aggregation
money_min(money(100, 'USD'), money(200, 'USD'), money(300, 'USD')); // Money::USD(100)
money_max(money(100, 'USD'), money(200, 'USD'), money(300, 'USD')); // Money::USD(300)
money_avg(money(100, 'USD'), money(200, 'USD'), money(300, 'USD')); // Money::USD(200)
money_sum(money(100, 'USD'), money(200, 'USD'), money(300, 'USD')); // Money::USD(600)

// Parsers
money_parse('$5.00'); // Money::USD(500)
money_parse_by_bitcoin("\xC9\x830.41"); // Money::XBT(41000000)
money_parse_by_decimal('1.00', 'USD'); // Money::USD(100)
money_parse_by_intl('$1.00'); // Money::USD(100)
money_parse_by_intl_localized_decimal('1.00', 'USD'); // Money::USD(100)
```

## Blade Extensions

```php
@currency() // To use default currency present in `config/money.php`
@currency('USD')
@money(500) // To use default currency present in `config/money.php`
@money(500, 'USD')

// Aggregation
@money_min(@money(100, 'USD'), @money(200, 'USD'), @money(300, 'USD')) // Money::USD(100)
@money_max(@money(100, 'USD'), @money(200, 'USD'), @money(300, 'USD')) // Money::USD(300)
@money_avg(@money(100, 'USD'), @money(200, 'USD'), @money(300, 'USD')) // Money::USD(200)
@money_sum(@money(100, 'USD'), @money(200, 'USD'), @money(300, 'USD')) // Money::USD(600)

// Parsers
@money_parse('$5.00') // Money::USD(500)
@money_parse_by_bitcoin("\xC9\x830.41") // Money::XBT(41000000)
@money_parse_by_decimal('1.00', 'USD') // Money::USD(100)
@money_parse_by_intl('$1.00') // Money::USD(100)
@money_parse_by_intl_localized_decimal('1.00', 'USD') // Money::USD(100)
```
