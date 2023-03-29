<?php
declare(strict_types=1);

namespace Hasfoug\Money;

trait LocaleTrait
{
    protected static string $locale;

    public static function getLocale(): string
    {
        if (! isset(static::$locale)) {
            static::setLocale(config('money.locale', 'en_US'));
        }

        return static::$locale;
    }

    public static function setLocale(string $locale): void
    {
        static::$locale = $locale;
    }
}
