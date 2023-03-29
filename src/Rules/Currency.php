<?php
declare(strict_types=1);

namespace Hasfoug\Money\Rules;

use Hasfoug\Money\Money;
use Illuminate\Contracts\Validation\Rule;

class Currency implements Rule
{
    public function passes($attribute, $value): bool
    {
        return Money::isValidCurrency($value);
    }

    public function message(): string
    {
        $message = trans('validation.currency');

        return $message === 'validation.currency'
            ? 'The :attribute is not a valid currency.'
            : $message;
    }
}
