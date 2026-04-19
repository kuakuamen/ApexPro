<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class BuildWorkoutxOverrides extends Command
{
    protected $signature = 'workoutx:build-overrides
        {--limit=0 : Limita quantidade de exercicios (0 = todos)}
        {--force : Reprocessa nomes ja mapeados}';

    protected $description = 'Gera mapeamento fixo de exercicios (DB) -> GIF WorkoutX em storage/app/workoutx_overrides.json';

    public function handle(): int
    {
        $apiKey = (string) config('services.workoutx.api_key');
        if ($apiKey === '') {
            $this->error('WORKOUTX_API_KEY nao configurada.');
            return self::FAILURE;
        }

        $file = storage_path('app/workoutx_overrides.json');
        $overrides = [];
        if (File::exists($file)) {
            $overrides = json_decode((string) File::get($file), true) ?: [];
        }

        $limit = max(0, (int) $this->option('limit'));
        $force = (bool) $this->option('force');

        $query = DB::table('exercises')
            ->select('name')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->distinct()
            ->orderBy('name');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $names = $query->pluck('name');
        $total = $names->count();
        $this->info("Exercicios distintos no banco: {$total}");

        $mapped = 0;
        $skipped = 0;
        $notFound = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($names as $name) {
            $norm = $this->normalize($name);
            if ($norm === '') {
                $skipped++;
                $bar->advance();
                continue;
            }

            if (!$force && isset($overrides[$norm])) {
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                $result = $this->resolveExercise($apiKey, (string) $name);
                if ($result) {
                    $overrides[$norm] = $result;
                    $mapped++;
                } else {
                    $notFound++;
                }
            } catch (\Throwable) {
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        ksort($overrides);
        File::put($file, json_encode($overrides, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->info("Concluido. mapeados={$mapped} | pulados={$skipped} | nao_encontrados={$notFound} | falhas={$failed}");
        $this->info("Arquivo: {$file}");

        return self::SUCCESS;
    }

    private function resolveExercise(string $apiKey, string $exerciseName): ?array
    {
        foreach ($this->candidates($exerciseName) as $candidate) {
            $response = Http::timeout(8)
                ->withHeaders([
                    'X-WorkoutX-Key' => $apiKey,
                    'Accept' => 'application/json',
                ])
                ->get('https://api.workoutxapp.com/v1/exercises/name/' . rawurlencode($candidate), [
                    'limit' => 10,
                ]);

            if (!$response->successful()) {
                continue;
            }

            $payload = $response->json();
            $items = collect(is_array($payload) && array_key_exists('data', $payload) ? $payload['data'] : $payload)
                ->filter(fn ($item) => is_array($item) && isset($item['name']))
                ->values();

            if ($items->isEmpty()) {
                continue;
            }

            $pick = $items->first(function (array $item) use ($exerciseName) {
                $name = $this->normalize((string) ($item['name'] ?? ''));
                if ($name === '') return false;
                if (!str_contains($this->normalize($exerciseName), 'smith') && str_contains($name, 'smith')) return false;
                return true;
            }) ?? $items->first();

            $gifUrl = (string) ($pick['gifUrl'] ?? '');
            if ($gifUrl === '') {
                continue;
            }

            if (!preg_match('~/gifs/(\d+)\.gif~', $gifUrl, $m)) {
                continue;
            }

            return [
                'provider' => 'workoutx',
                'media_type' => 'gif',
                'gif_id' => $m[1],
                'title' => (string) ($pick['name'] ?? $exerciseName),
            ];
        }

        return null;
    }

    private function candidates(string $exerciseName): array
    {
        $name = $this->normalize($exerciseName);
        $map = [
            'agachamento sumo' => 'sumo squat',
            'agachamento' => 'squat',
            'levantamento terra romeno' => 'romanian deadlift',
            'levantamento terra' => 'deadlift',
            'supino reto com barra' => 'barbell bench press',
            'supino reto' => 'bench press',
            'supino inclinado' => 'incline bench press',
            'supino declinado' => 'decline bench press',
            'crucifixo inclinado com halteres' => 'incline dumbbell fly',
            'crucifixo inclinado' => 'incline fly',
            'crucifixo' => 'fly',
            'desenvolvimento com halteres sentado' => 'seated shoulder press',
            'desenvolvimento sentado com halteres' => 'seated shoulder press',
            'desenvolvimento com halteres' => 'shoulder press',
            'desenvolvimento militar' => 'shoulder press',
            'elevacao lateral' => 'lateral raise',
            'rosca direta' => 'barbell curl',
            'rosca alternada' => 'dumbbell curl',
            'triceps testa' => 'skull crusher',
            'triceps pulley' => 'triceps pushdown',
            'afundo' => 'lunge',
            'cadeira extensora' => 'leg extension',
            'extensora' => 'leg extension',
            'mesa flexora' => 'leg curl',
            'flexora deitada' => 'lying leg curl',
            'flexora' => 'leg curl',
            'panturrilha em pe' => 'standing calf raise',
            'panturrilha sentado' => 'seated calf raise',
            'elevacao pelvica com barra' => 'barbell glute bridge',
            'elevacao pelvica' => 'glute bridge',
            'gluteo na maquina' => 'lever hip extension',
            'gluteo maquina' => 'lever hip extension',
            'stiff' => 'romanian deadlift',
            'passada' => 'walking lunge',
            'com halteres' => 'dumbbell',
            'halteres' => 'dumbbell',
            'com barra' => 'barbell',
            'barra' => 'barbell',
        ];

        $translated = $name;
        foreach ($map as $pt => $en) {
            if (str_contains($translated, $pt)) {
                $translated = str_replace($pt, $en, $translated);
            }
        }

        return collect([
            trim($translated),
            trim(preg_replace('/\s+/', ' ', (string) preg_replace('/\bcom\b/', '', $translated))),
            trim($name),
        ])->filter()->unique()->values()->all();
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        $text = preg_replace('/[^a-z0-9]+/', ' ', $text) ?? $text;
        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }
}

