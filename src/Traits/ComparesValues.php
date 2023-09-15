<?php
declare(strict_types=1);

namespace Hasfoug\Money\Traits;

trait ComparesValues
{
    public function isNotZero(): bool
    {
        return ! $this->isZero();
    }

    public function eq(mixed $other): bool
    {
        return $this->equals($other);
    }

    public function gt(mixed $other): bool
    {
        return $this->greaterThan($other);
    }

    public function gte(mixed $other): bool
    {
        return $this->greaterThanOrEqual($other);
    }

    public function lt(mixed $other): bool
    {
        return $this->lessThan($other);
    }

    public function lte(mixed $other): bool
    {
        return $this->lessThanOrEqual($other);
    }

    public function equals(mixed $other): bool
    {
        return $this->money->equals(self::parse($other, $this->money->getCurrency())->money);
    }

    public function greaterThan(mixed $other): bool
    {
        return $this->money->greaterThan(self::parse($other, $this->money->getCurrency())->money);
    }

    public function greaterThanOrEqual(mixed $other): bool
    {
        return $this->money->greaterThanOrEqual(self::parse($other, $this->money->getCurrency())->money);
    }

    public function lessThan(mixed $other): bool
    {
        return $this->money->lessThan(self::parse($other, $this->money->getCurrency())->money);
    }

    public function lessThanOrEqual(mixed $other): bool
    {
        return $this->money->lessThanOrEqual(self::parse($other, $this->money->getCurrency())->money);
    }

    public function compare(mixed $other): int
    {
        return $this->money->compare(self::parse($other, $this->money->getCurrency())->money);
    }
}
