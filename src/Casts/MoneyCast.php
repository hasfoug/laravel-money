<?php
declare(strict_types=1);

namespace Hasfoug\Money\Casts;

use Hasfoug\Money\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

abstract class MoneyCast implements CastsAttributes
{
    protected ?string $currency;

    protected bool $forceDecimals = false;

    public function __construct(string $currency = 'currency', ?bool $forceDecimals = null)
    {
        $this->currency = $currency;
        $this->forceDecimals = is_string($forceDecimals)
            ? filter_var($forceDecimals, FILTER_VALIDATE_BOOLEAN)
            : (bool) $forceDecimals;
    }

    abstract protected function getFormatter(Money $money): mixed;

    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return \Hasfoug\Money\Money|null
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        return Money::parse($value, $this->getCurrency($attributes), $this->forceDecimals);
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return [$key => $value];
        }

        try {
            $money = Money::parse($value, $this->getCurrency($attributes), $this->forceDecimals);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(
                sprintf('Invalid data provided for %s::$%s', get_class($model), $key)
            );
        }

        $amount = $this->getFormatter($money);

        if ($this->currency && ! Money::isValidCurrency($this->currency)) {
            return [$key => $amount, $this->currency => $money->getCurrency()->getCode()];
        }

        return [$key => $amount];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  Money  $value
     * @param  array  $attributes
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function serialize($model, string $key, $value, array $attributes): mixed
    {
        return $value->serialize();
    }

    public function increment($model, string $key, $value, array $attributes): mixed
    {
        return $model->{$key}->add($value);
    }

    public function decrement($model, string $key, $value, array $attributes): mixed
    {
        return $model->{$key}->subtract($value);
    }

    /**
     * Get currency.
     *
     * @param  array  $attributes
     * @return \Money\Currency
     */
    protected function getCurrency(array $attributes)
    {
        $defaultCode = Money::getDefaultCurrency();

        if ($this->currency === null) {
            return Money::parseCurrency($defaultCode);
        }

        $currency = Money::parseCurrency($this->currency);
        $currencies = Money::getCurrencies();

        if ($currencies->contains($currency)) {
            return $currency;
        }

        $code = $attributes[$this->currency] ?? $defaultCode;

        return Money::parseCurrency($code);
    }
}
