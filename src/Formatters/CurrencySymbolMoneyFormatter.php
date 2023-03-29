<?php
declare(strict_types=1);

namespace Hasfoug\Money\Formatters;

use Hasfoug\Money\Money;
use Money\Currencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\MoneyFormatter;
use NumberFormatter;

class CurrencySymbolMoneyFormatter implements MoneyFormatter
{
    protected bool $right;

    protected string $locale;

    protected \Money\Currencies $currencies;

    public function __construct(bool $right = false, ?string $locale = null, ?Currencies $currencies = null)
    {
        $this->right = $right;
        $this->locale = $locale ?: Money::getLocale();
        $this->currencies = $currencies ?: Money::getCurrencies();
    }

    public function format(\Money\Money $money): string
    {
        $numberFormatter = new NumberFormatter($this->locale, NumberFormatter::DECIMAL);
        $symbol = $numberFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL);

        $formatter = new DecimalMoneyFormatter($this->currencies);
        $value = $formatter->format($money);

        return $this->right ? $value.$symbol : $symbol.$value;
    }
}
