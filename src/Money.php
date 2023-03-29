<?php
declare(strict_types=1);

namespace Hasfoug\Money;

use Hasfoug\Money\Traits\CurrenciesTrait;
use Hasfoug\Money\Traits\ComparesValues;
use Hasfoug\Money\Traits\MoneyFormatterTrait;
use Hasfoug\Money\Traits\MoneyParserTrait;
use Hasfoug\Money\Traits\MoneySerializerTrait;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Money\Currencies;
use Money\Currency;

/**
 * @method bool isSameCurrency(Money|\Money\Money ...$others)
 * @method \Money\Currency getCurrency()
 * @method Money[] allocate(array $ratios)
 * @method Money[] allocateTo(int $n)
 * @method string ratioOf(Money|\Money\Money $money)
 * @method Money absolute()
 * @method Money negative()
 * @method bool isZero()
 * @method bool isPositive()
 * @method bool isNegative()
 */
class Money implements Arrayable, Jsonable, JsonSerializable, Renderable
{
    use CurrenciesTrait;
    use LocaleTrait;
    use MoneyFactory {
        MoneyFactory::__callStatic as factoryCallStatic;
    }
    use MoneyFormatterTrait;
    use MoneySerializerTrait;
    use MoneyParserTrait;
    use ComparesValues;
    use Macroable {
        Macroable::__call as macroCall;
    }

    protected \Money\Money $money;

    protected array $attributes = [];

    public function __construct(
        mixed $amount = null,
        Currency|string|null $currency = null,
        bool $forceDecimals = false,
        string|null $locale = null,
        Currencies|null $currencies = null
    ) {
        $this->money = Money::parse($amount, $currency, $forceDecimals, $locale, $currencies, null, false);
    }

    /**
     * @return \Hasfoug\Money\Money|\Hasfoug\Money\Money[]|mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if (! method_exists($this->money, $method)) {
            return $this;
        }

        $result = call_user_func_array([$this->money, $method], static::getArguments($parameters));

        $methods = [
            'allocate', 'allocateTo',
            'absolute', 'negative',
        ];

        if (! in_array($method, $methods)) {
            return $result;
        }

        return static::convertResult($result);
    }

    public static function __callStatic($method, array $parameters): self
    {
        return static::factoryCallStatic($method, $parameters);
    }

    public function getMoney(): \Money\Money
    {
        return $this->money;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes = []): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function add(mixed ...$addends): self
    {
        $addends = collect($addends)->map(fn (mixed $addend) => self::parse($addend)->money);

        return new self(
            $this->money->add(...$addends)
        );
    }

    public function subtract(mixed ...$addends): self
    {
        $addends = collect($addends)->map(fn (mixed $addend) => self::parse($addend)->money);

        return new self(
            $this->money->subtract(...$addends)
        );
    }

    public function mod(mixed $divisor): self
    {
        return new self(
            $this->money->mod(self::parse($divisor)->money)
        );
    }

    public function divide(int|string|float $divisor, int $roundingMode = \Money\Money::ROUND_HALF_UP): self
    {
        return new self(
            $this->money->divide((string) $divisor, $roundingMode)
        );
    }

    public function multiply(int|string|float $multiplier, int $roundingMode = \Money\Money::ROUND_HALF_UP): self
    {
        return new self(
            $this->money->multiply((string) $multiplier, $roundingMode)
        );
    }

    public static function min(mixed $first, mixed ...$collection): self
    {
        $first = self::parse($first)->money;
        $collection = collect($collection)->map(fn (mixed $item) => self::parse($item)->money);

        return new self(
            \Money\Money::min($first, ...$collection)
        );
    }

    public static function max(mixed $first, mixed ...$collection): self
    {
        $first = self::parse($first)->money;
        $collection = collect($collection)->map(fn (mixed $item) => self::parse($item)->money);

        return new self(
            \Money\Money::max($first, ...$collection)
        );
    }

    public static function sum(mixed $first, mixed ...$collection): self
    {
        $first = self::parse($first)->money;
        $collection = collect($collection)->map(fn (mixed $item) => self::parse($item)->money);

        return new self(
            \Money\Money::sum($first, ...$collection)
        );
    }

    public static function avg(mixed $first, mixed ...$collection): self
    {
        $first = self::parse($first)->money;
        $collection = collect($collection)->map(fn (mixed $item) => self::parse($item)->money);

        return new self(
            \Money\Money::avg($first, ...$collection)
        );
    }

    public function getAmount(): int
    {
        return (int) $this->money->getAmount();
    }

    public function jsonSerialize(): mixed
    {
        return $this->serialize();
    }

    public function toArray(): array
    {
        return $this->serializeByArray();
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function render(): mixed
    {
        return $this->format();
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }

    public static function convert(\Money\Money $instance): self
    {
        return static::fromMoney($instance);
    }

    private static function getArguments(array $arguments = []): array
    {
        $args = [];

        foreach ($arguments as $argument) {
            $args[] = $argument instanceof static ? $argument->getMoney() : $argument;
        }

        return $args;
    }

    /**
     * @return \Hasfoug\Money\Money|\Hasfoug\Money\Money[]
     */
    private static function convertResult($result): self|array
    {
        if (! is_array($result)) {
            return static::convert($result);
        }

        $results = [];

        foreach ($result as $item) {
            $results[] = static::convert($item);
        }

        return $results;
    }
}
