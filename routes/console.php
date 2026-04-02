<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Verifica inadimplência e suspende alunos com +5 dias de atraso — roda diariamente à meia-noite
Schedule::command('financial:suspend-overdue')->dailyAt('00:00');

// Verifica assinaturas de profissionais vencidas e suspende após grace period
Schedule::command('subscription:check-overdue')->dailyAt('06:00');
