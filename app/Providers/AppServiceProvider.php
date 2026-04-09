<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Converte datas UTC do banco para exibição em horário de Brasília (UTC-3)
        Blade::directive('brdate', function ($expression) {
            return "<?php
                \$_d = $expression;
                if (\$_d) {
                    echo (\$_d instanceof \Carbon\Carbon ? \$_d : \Carbon\Carbon::parse(\$_d))
                        ->setTimezone('America/Sao_Paulo')
                        ->format('d/m/Y');
                } else {
                    echo '—';
                }
            ?>";
        });

        Blade::directive('brdatetime', function ($expression) {
            return "<?php
                \$_d = $expression;
                if (\$_d) {
                    echo (\$_d instanceof \Carbon\Carbon ? \$_d : \Carbon\Carbon::parse(\$_d))
                        ->setTimezone('America/Sao_Paulo')
                        ->format('d/m/Y H:i');
                } else {
                    echo '—';
                }
            ?>";
        });
    }
}
