<?php
declare(strict_types=1);

namespace Hasfoug\Money\Traits;

use InvalidArgumentException;
use Money\Currencies;
use Money\Currencies\AggregateCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\CurrencyList;
use Money\Currencies\ISOCurrencies;
use Money\Currency;

trait CurrenciesTrait
{
    protected static string $currency;

    protected static Currencies $currencies;

    protected static array $ISOCurrencies;

    public static function parseCurrency($currency): ?Currency
    {
        if (is_string($currency)) {
            return new Currency($currency);
        }

        return $currency;
    }

    public static function isValidCurrency(Currency|string $currency): bool
    {
        return static::getCurrencies()->contains(static::parseCurrency($currency));
    }

    public static function getDefaultCurrency(): string
    {
        if (! isset(static::$currency)) {
            static::setDefaultCurrency(config('money.defaultCurrency', config('money.currency', 'USD')));
        }

        return static::$currency;
    }

    public static function setDefaultCurrency(string $currency): void
    {
        static::$currency = $currency;
    }

    public static function getISOCurrencies(): array
    {
        if (! isset(static::$ISOCurrencies) && is_file($file = config('money.isoCurrenciesPath'))) {
            static::$ISOCurrencies = require $file;
        }

        return static::$ISOCurrencies;
    }

    public static function getCurrencies(): Currencies
    {
        if (! isset(static::$currencies)) {
            static::setCurrencies(config('money.currencies', []));
        }

        return static::$currencies;
    }

    public static function setCurrencies(mixed $currencies): void
    {
        static::$currencies = ($currencies instanceof Currencies)
            ? $currencies
            : static::makeCurrencies($currencies);
    }

    private static function makeCurrencies(array|null $currenciesConfig): Currencies
    {
        if (! $currenciesConfig || ! is_array($currenciesConfig)) {
            // for backward compatibility
            return new ISOCurrencies();
        }

        $currenciesList = [];

        if ($currenciesConfig['iso'] ?? false) {
            $currenciesList[] = static::makeCurrenciesForSource(
                $currenciesConfig['iso'],
                new ISOCurrencies(),
                'ISO'
            );
        }

        if ($currenciesConfig['bitcoin'] ?? false) {
            $currenciesList[] = static::makeCurrenciesForSource(
                $currenciesConfig['bitcoin'],
                new BitcoinCurrencies(),
                'Bitcoin'
            );
        }

        if ($currenciesConfig['custom'] ?? false) {
            $currenciesList[] = new CurrencyList($currenciesConfig['custom']);
        }

        return new AggregateCurrencies($currenciesList);
    }

    /**
     * Make currencies list according to array for specified source.
     *
     * @throws \InvalidArgumentException
     */
    private static function makeCurrenciesForSource(mixed $config, Currencies $currencies, string $sourceName): Currencies
    {
        if ($config === 'all') {
            return $currencies;
        }

        if (is_array($config)) {
            $lisCurrencies = [];

            foreach ($config as $index => $currencyCode) {
                $currency = static::parseCurrency($currencyCode);

                if (! $currencies->contains($currency)) {
                    throw new InvalidArgumentException(
                        sprintf('Unknown %s currency code: %s', $sourceName, $currencyCode)
                    );
                }

                $lisCurrencies[$currency->getCode()] = $currencies->subunitFor($currency);
            }

            return new CurrencyList($lisCurrencies);
        }

        throw new InvalidArgumentException(
            sprintf('%s config must be an array or \'all\'', $sourceName)
        );
    }
}
