<?php

namespace Hasfoug\Money\Tests\Database\Models;

use Hasfoug\Money\Casts\MoneyDecimalCast;
use Hasfoug\Money\Casts\MoneyIntegerCast;
use Hasfoug\Money\Casts\MoneyStringCast;
use Illuminate\Database\Eloquent\Model;

/**
 * The testing user model.
 */
class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'money',
        'wage',
        'debits',
        'credits',
        'currency',
    ];

    /**
     * The attributes to cast.
     *
     * @var array
     */
    protected $casts = [
        'money' => MoneyStringCast::class,
        'wage' => MoneyIntegerCast::class.':EUR',
        'debits' => MoneyDecimalCast::class.':currency',
        'credits' => MoneyDecimalCast::class.':USD,true',
    ];
}
