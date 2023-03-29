<?php
declare(strict_types=1);

namespace Hasfoug\Money;

use Illuminate\View\Compilers\BladeCompiler;

class BladeExtension
{
    public static function register(BladeCompiler $compiler): void
    {
        $compiler->directive('currency', function ($expression) {
            return "<?php echo currency({$expression}); ?>";
        });

        $compiler->directive('money', function ($expression) {
            return "<?php echo money({$expression}); ?>";
        });

        self::registerAggregations($compiler);
        self::registerParsers($compiler);
    }

    private static function registerAggregations(BladeCompiler $compiler): void
    {
        $compiler->directive('money_min', function ($expression) {
            return "<?php echo money_min({$expression}); ?>";
        });

        $compiler->directive('money_max', function ($expression) {
            return "<?php echo money_max({$expression}); ?>";
        });

        $compiler->directive('money_avg', function ($expression) {
            return "<?php echo money_avg({$expression}); ?>";
        });

        $compiler->directive('money_sum', function ($expression) {
            return "<?php echo money_sum({$expression}); ?>";
        });
    }

    private static function registerParsers(BladeCompiler $compiler): void
    {
        $compiler->directive('money_parse', function ($expression) {
            return "<?php echo money_parse({$expression}); ?>";
        });

        $compiler->directive('money_parse_by_bitcoin', function ($expression) {
            return "<?php echo money_parse_by_bitcoin({$expression}); ?>";
        });

        $compiler->directive('money_parse_by_decimal', function ($expression) {
            return "<?php echo money_parse_by_decimal({$expression}); ?>";
        });

        $compiler->directive('money_parse_by_intl', function ($expression) {
            return "<?php echo money_parse_by_intl({$expression}); ?>";
        });

        $compiler->directive('money_parse_by_intl_localized_decimal', function ($expression) {
            return "<?php echo money_parse_by_intl_localized_decimal({$expression}); ?>";
        });
    }
}
