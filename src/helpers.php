<?php

use Hasfoug\Money\Money;
use Money\Currencies;
use Money\Currency;

if (! function_exists('currency')) {
    function currency(Currency|string|null $currency = null): Currency
    {
        return Hasfoug\Money\Money::parseCurrency($currency ?: Hasfoug\Money\Money::getDefaultCurrency());
    }
}

if (! function_exists('money')) {
    function money(
        mixed $amount = null,
        Currency|string|null $currency = null,
        bool $forceDecimals = false,
        ?string $locale = null,
        ?Currencies $currencies = null
    ): Money
    {
        return new Hasfoug\Money\Money($amount, $currency, $forceDecimals, $locale, $currencies);
    }
}

if (! function_exists('money_min')) {
    function money_min(mixed $first, mixed ...$collection): Money
    {
        return Hasfoug\Money\Money::min($first, ...$collection);
    }
}

if (! function_exists('money_max')) {
    function money_max(mixed $first, mixed ...$collection): Money
    {
        return Hasfoug\Money\Money::max($first, ...$collection);
    }
}

if (! function_exists('money_avg')) {
    function money_avg(mixed $first, mixed ...$collection): Money
    {
        return Hasfoug\Money\Money::avg($first, ...$collection);
    }
}

if (! function_exists('money_sum')) {
    function money_sum(mixed $first, mixed ...$collection): Money
    {
        return Hasfoug\Money\Money::sum($first, ...$collection);
    }
}

if (! function_exists('money_parse')) {
    function money_parse(
        mixed $value,
        Currency|string|null $currency = null,
        bool$forceDecimals = false,
        ?string $locale = null,
        ?Currencies $currencies = null,
        int $bitcoinDigits = null,
        bool $convert = true
    ): Money
    {
        return Hasfoug\Money\Money::parse(
            $value,
            $currency,
            $forceDecimals,
            $locale,
            $currencies,
            $bitcoinDigits,
            $convert
        );
    }
}

if (! function_exists('money_parse_by_bitcoin')) {
    function money_parse_by_bitcoin(
        mixed $money,
        Currency|string|null $fallbackCurrency = null,
        ?int $fractionDigits = null,
        bool $convert = true,
    ): Money
    {
        return Hasfoug\Money\Money::parseByBitcoin(
            $money,
            $fallbackCurrency,
            $fractionDigits,
            $convert,
        );
    }
}

if (! function_exists('money_parse_by_decimal')) {
    function money_parse_by_decimal(
        mixed $money,
        Currency|string|null $fallbackCurrency = null,
        ?Currencies $currencies = null,
        bool $convert = true,
    ): Money
    {
        return Hasfoug\Money\Money::parseByDecimal(
            $money,
            $fallbackCurrency,
            $currencies,
            $convert
        );
    }
}

if (! function_exists('money_parse_by_intl')) {
    function money_parse_by_intl(
        mixed $money,
        Currency|string|null $fallbackCurrency = null,
        ?string $locale = null,
        ?Currencies $currencies = null,
        mixed $style = null,
        bool $convert = true,
    ): Money
    {
        return Hasfoug\Money\Money::parseByIntl(
            $money,
            $fallbackCurrency,
            $locale,
            $currencies,
            $style,
            $convert
        );
    }
}

if (! function_exists('money_parse_by_intl_localized_decimal')) {
    function money_parse_by_intl_localized_decimal(
        mixed $money,
        Currency|string|null $fallbackCurrency = null,
        ?string $locale = null,
        ?Currencies $currencies = null,
        mixed $style = null,
        bool $convert = true
    ): Money
    {
        return Hasfoug\Money\Money::parseByIntlLocalizedDecimal(
            $money,
            $fallbackCurrency,
            $locale,
            $currencies,
            $style,
            $convert
        );
    }
}
