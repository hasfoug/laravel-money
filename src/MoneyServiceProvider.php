<?php
declare(strict_types=1);

namespace Hasfoug\Money;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class MoneyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'money');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/config.php' => config_path('money.php')], 'config');
        }

        $this->callAfterResolving(BladeCompiler::class, function ($blade) {
            BladeExtension::register($blade);
        });

        Validator::extend('currency', function ($attribute, $value) {
            $rule = new Rules\Currency();

            return $rule->passes($attribute, $value);
        });

        Validator::extend('money', function ($attribute, $value, $parameters) {
            $rule = new Rules\Money(...$parameters);

            return $rule->passes($attribute, $value);
        });
    }
}
