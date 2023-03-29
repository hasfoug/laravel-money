<?php
declare(strict_types=1);

namespace Hasfoug\Money\Rules;

use Illuminate\Contracts\Validation\Rule;
use InvalidArgumentException;
use Money\Exception\ParserException;

class Money implements Rule
{
    protected \Money\Currency|string|null $currency;

    protected ?string $locale;

    protected ?\Money\Currencies $currencies;

    protected ?int $bitcoinDigits;

    public function __construct(
        \Money\Currency|string|null $currency = null,
        ?string $locale = null,
        ?\Money\Currencies $currencies = null,
        ?int $bitcoinDigits = null
    ) {
        $this->currency = $currency;
        $this->locale = $locale;
        $this->currencies = $currencies;
        $this->bitcoinDigits = $bitcoinDigits;
    }

    public function passes($attribute, $value): bool
    {
        try {
            $money = \Hasfoug\Money\Money::parse(
                $value,
                $this->currency,
                false,
                $this->locale,
                $this->currencies,
                $this->bitcoinDigits
            );

            return ! (
                $this->currency && ! $money->getCurrency()->equals(\Hasfoug\Money\Money::parseCurrency($this->currency))
            );
        } catch (InvalidArgumentException|ParserException $e) {
            return false;
        }
    }

    public function message(): string
    {
        $message = trans('validation.money');

        return $message === 'validation.money'
            ? 'The :attribute is not a valid money.'
            : $message;
    }
}
