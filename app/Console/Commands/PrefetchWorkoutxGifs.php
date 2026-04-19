<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class PrefetchWorkoutxGifs extends Command
{
    protected $signature = 'workoutx:prefetch-gifs
        {count=200 : Quantidade de GIFs para baixar}
        {--start=1 : ID inicial (numero)}
        {--force : Rebaixa mesmo se arquivo ja existir}';

    protected $description = 'Pre-baixa GIFs da WorkoutX para cache local em storage/app/workoutx_gifs';

    public function handle(): int
    {
        $apiKey = (string) config('services.workoutx.api_key');
        if ($apiKey === '') {
            $this->error('WORKOUTX_API_KEY nao configurada.');
            return self::FAILURE;
        }

        $count = max(1, (int) $this->argument('count'));
        $start = max(1, (int) $this->option('start'));
        $force = (bool) $this->option('force');

        $dir = storage_path('app/workoutx_gifs');
        File::ensureDirectoryExists($dir);

        $downloaded = 0;
        $skipped = 0;
        $missing = 0;
        $failed = 0;

        $this->info("Prefetch WorkoutX: start={$start}, count={$count}");
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = $start; $i < ($start + $count); $i++) {
            $gifId = str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            $localPath = $dir . DIRECTORY_SEPARATOR . $gifId . '.gif';

            if (!$force && File::exists($localPath)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                $response = Http::timeout(12)
                    ->withHeaders([
                        'X-WorkoutX-Key' => $apiKey,
                        'Accept' => 'image/gif',
                    ])
                    ->get("https://api.workoutxapp.com/v1/gifs/{$gifId}.gif");

                if ($response->status() === 404) {
                    $missing++;
                    $bar->advance();
                    continue;
                }

                if (!$response->successful()) {
                    $failed++;
                    $bar->advance();
                    continue;
                }

                File::put($localPath, $response->body());
                $downloaded++;
            } catch (\Throwable) {
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Concluido. baixados={$downloaded} | ja_existiam={$skipped} | nao_encontrados={$missing} | falhas={$failed}");

        return self::SUCCESS;
    }
}

