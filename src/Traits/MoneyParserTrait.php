<?php
declare(strict_types=1);

namespace Hasfoug\Money\Traits;

use Hasfoug\Money\Money;
use InvalidArgumentException;
use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\MoneyParser;
use Money\Parser\AggregateMoneyParser;
use Money\Parser\BitcoinMoneyParser;
use Money\Parser\DecimalMoneyParser;
use Money\Parser\IntlLocalizedDecimalParser;
use Money\Parser\IntlMoneyParser;
use NumberFormatter;

trait MoneyParserTrait
{
    /**
     * Convert the given value into an instance of Money.
     *
     * @throws \InvalidArgumentException
     */
    public static function parse(
        mixed $value,
        Currency|string|null $currency = null,
        bool $forceDecimals = false,
        ?string $locale = null,
        ?Currencies $currencies = null,
        ?int $bitcoinDigits = null,
        bool $convert = true
    ): Money|\Money\Money
    {
        $value = is_null($value) ? (int) $value : $value;

        if ($value instanceof Money) {
            return $convert ? $value : $value->getMoney();
        }

        if ($value instanceof \Money\Money) {
            return $convert ? static::fromMoney($value) : $value;
        }

        if (! is_scalar($value)) {
            throw new InvalidArgumentException(sprintf('Invalid value %s', json_encode($value)));
        }

        if (
            (is_int($value) || (filter_var($value, FILTER_VALIDATE_INT) !== false && ! is_float($value)))
            && $forceDecimals
        ) {
            $value = sprintf('%.14F', $value);
        }

        $currency = static::parseCurrency($currency ?: static::getDefaultCurrency());

        if (is_int($value) || (filter_var($value, FILTER_VALIDATE_INT) !== false && ! is_float($value))) {
            return $convert
                ? new Money($value, $currency)
                : new \Money\Money($value, $currency);
        }

        $currencies = $currencies ?: static::getCurrencies();

        if (is_float($value) || filter_var($value, FILTER_VALIDATE_FLOAT)) {
            return static::parseByDecimal($value, $currency, $currencies, $convert);
        }

        $locale = $locale ?: static::getLocale();

        $parsers = [
            new IntlMoneyParser(new NumberFormatter($locale, NumberFormatter::DECIMAL), $currencies),
            new IntlLocalizedDecimalParser(new NumberFormatter($locale, NumberFormatter::DECIMAL), $currencies),
            new DecimalMoneyParser($currencies),
            new BitcoinMoneyParser($bitCointDigits ?? 2),
        ];

        try {
            return static::parseByAggregate($value, null, $parsers, $convert);
        } catch (ParserException $e) {
            return static::parseByAggregate($value, $currency, $parsers, $convert);
        }
    }

    /**
     * Parse by aggregate.
     *
     * @param MoneyParser[] $parsers
     */
    public static function parseByAggregate(
        mixed $money,
        Currency|string|null $fallbackCurrency = null,
        array $parsers = [],
        bool $convert = true,
    ): Money|\Money\Money
    {
        $parser = new AggregateMoneyParser($parsers);

        return static::parseByParser($parser, $money, $fallbackCurrency, $convert);
    }

    public static function parseByBitcoin(
        mixed $money,
        Currency|string|null$fallbackCurrency = null,
        ?int $fractionDigits = null,
        bool $convert = true,
    ): Money|\Money\Money
    {
        $parser = new BitcoinMoneyParser($fractionDigits ?? 2);

        return static::parseByParser($parser, $money, $fallbackCurrency, $convert);
    }

    public static function parseByDecimal(
        mixed $money,
        Currency|string|null $fallbackCurrency = null,
        ?Currencies $currencies = null,
        bool $convert = true
    ): Money|\Money\Money
    {
        $parser = new DecimalMoneyParser($currencies ?: static::getCurrencies());

        return static::parseByParser($parser, $money, $fallbackCurrency, $convert);
    }

    public static function parseByIntl(
        mixed $money,
        Currency|string|null $fallbackCurrency = null,
        ?string $locale = null,
        ?Currencies $currencies = null,
        mixed $style = null,
        bool $convert = true
    ): Money|\Money\Money
    {
        $numberFormatter = new NumberFormatter(
            $locale ?: static::getLocale(),
            $style ?: NumberFormatter::DECIMAL
        );

        $parser = new IntlMoneyParser($numberFormatter, $currencies ?: static::getCurrencies());

        return static::parseByParser($parser, $money, $fallbackCurrency, $convert);
    }

    public static function parseByIntlLocalizedDecimal(
        mixed $money,
        Currency|string|null $fallbackCurrency = null,
        ?string $locale = null,
        ?Currencies $currencies = null,
        mixed $style = null,
        bool $convert = true,
    ): Money|\Money\Money
    {
        $numberFormatter = new NumberFormatter(
            $locale ?: static::getLocale(),
            $style ?: NumberFormatter::DECIMAL
        );

        $parser = new IntlLocalizedDecimalParser($numberFormatter, $currencies ?: static::getCurrencies());

        return static::parseByParser($parser, $money, $fallbackCurrency, $convert);
    }

    public static function parseByParser(
        MoneyParser $parser,
        mixed $money,
        Currency|string|null $fallbackCurrency = null,
        bool $convert = true
    ): Money|\Money\Money
    {
        $fallbackCurrency = static::parseCurrency($fallbackCurrency);
        $originalMoney = $parser->parse((string) $money, $fallbackCurrency);

        return $convert ? static::convert($originalMoney) : $originalMoney;
    }
}
