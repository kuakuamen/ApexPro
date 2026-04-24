<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DietAiService
{
    protected array $apiKeys = [];

    public function __construct()
    {
        $keys = array_filter([
            config('services.gemini.api_key'),
            config('services.gemini.api_key_2'),
            config('services.gemini.api_key_3'),
        ]);

        if (empty($keys)) {
            Log::error('GEMINI_API_KEY nao esta configurada para DietAiService.');
            return;
        }

        $this->apiKeys = array_values($keys);
    }

    protected function callWithFallback(callable $fn): mixed
    {
        $combinations = [];

        foreach ($this->apiKeys as $key) {
            $combinations[] = ['key' => $key, 'model' => 'gemini-2.5-flash'];
        }

        if (!empty($this->apiKeys)) {
            $lastKey = end($this->apiKeys);
            $combinations[] = ['key' => $lastKey, 'model' => 'gemini-2.5-flash-lite'];
            $combinations[] = ['key' => $lastKey, 'model' => 'gemini-3.1-flash-lite-preview'];
        }

        $lastException = null;

        foreach ($combinations as $combo) {
            try {
                $client = \Gemini::client($combo['key']);
                return $fn($client, $combo['model']);
            } catch (\Throwable $e) {
                $lastException = $e;
                $isRetryable = str_contains($e->getMessage(), 'Quota exceeded')
                    || str_contains($e->getMessage(), 'quota')
                    || str_contains($e->getMessage(), '429')
                    || str_contains($e->getMessage(), 'RESOURCE_EXHAUSTED')
                    || str_contains($e->getMessage(), 'high demand')
                    || str_contains($e->getMessage(), 'temporarily')
                    || str_contains($e->getMessage(), '503');

                if (!$isRetryable) {
                    throw $e;
                }
            }
        }

        if ($lastException) {
            throw $lastException;
        }

        throw new \RuntimeException('Nao foi possivel processar a dieta com IA.');
    }

    public function generateDiet(array $studentData): array
    {
        if (empty($this->apiKeys)) {
            throw new \RuntimeException('Chave API do Gemini nao esta configurada.');
        }

        $goal = trim((string) ($studentData['goal'] ?? ''));
        $initialKcal = $studentData['initial_kcal'] ?? null;
        $studentName = trim((string) ($studentData['student_name'] ?? 'Aluno'));
        $age = $studentData['age'] ?? null;
        $gender = trim((string) ($studentData['gender'] ?? 'Nao informado'));
        $weight = $studentData['weight'] ?? null;
        $height = $studentData['height'] ?? null;
        $bodyFat = $studentData['body_fat'] ?? null;

        $prompt = "Voce e um nutricionista esportivo e deve montar um plano alimentar inicial para um aluno.\n\n" .
            "DADOS DO ALUNO:\n" .
            "- Nome: {$studentName}\n" .
            "- Idade: " . ($age ?: 'Nao informada') . "\n" .
            "- Genero: {$gender}\n" .
            "- Peso (kg): " . ($weight ?: 'Nao informado') . "\n" .
            "- Altura (m): " . ($height ?: 'Nao informada') . "\n" .
            "- Percentual de gordura: " . ($bodyFat ?: 'Nao informado') . "\n" .
            "- Objetivo: " . ($goal !== '' ? $goal : 'Plano alimentar geral') . "\n" .
            "- Kcal dia informada pelo personal: " . ($initialKcal ?: 'Nao informado') . "\n\n" .
            "REGRAS:\n" .
            "1. Retorne somente JSON valido, sem markdown.\n" .
            "2. Monte de 4 a 6 refeicoes.\n" .
            "3. Em cada refeicao, inclua ao menos 2 alimentos.\n" .
            "4. Campo calories deve ser string numerica aproximada por alimento.\n" .
            "5. O plano precisa ser pratico para rotina real.\n" .
            "6. Linguagem e nomes em portugues.\n\n" .
            "ESTRUTURA OBRIGATORIA DO JSON:\n" .
            "{\n" .
            "  \"name\": \"string\",\n" .
            "  \"goal\": \"string\",\n" .
            "  \"daily_kcal_target\": 2200,\n" .
            "  \"meals\": [\n" .
            "    {\n" .
            "      \"name\": \"Cafe da manha\",\n" .
            "      \"time\": \"07:00\",\n" .
            "      \"foods\": [\n" .
            "        {\n" .
            "          \"name\": \"Ovos mexidos\",\n" .
            "          \"quantity\": \"2 unidades\",\n" .
            "          \"calories\": \"140\",\n" .
            "          \"observation\": \"opcional\"\n" .
            "        }\n" .
            "      ]\n" .
            "    }\n" .
            "  ]\n" .
            "}\n";

        try {
            $response = $this->callWithFallback(
                fn($client, $model) => $client->generativeModel($model)->generateContent($prompt)
            );

            $textResult = trim((string) $response->text());
            $textResult = preg_replace('/^```json\s*|\s*```$/', '', $textResult);
            $jsonResult = json_decode($textResult, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($jsonResult)) {
                Log::error('Erro JSON Gemini Diet: ' . substr($textResult, 0, 1000));
                throw new \RuntimeException('A IA retornou uma resposta invalida para dieta.');
            }

            return $this->normalizePayload($jsonResult, $goal, $initialKcal, $studentName);
        } catch (\Throwable $e) {
            Log::error('Erro API Gemini Diet: ' . $e->getMessage());

            if (str_contains($e->getMessage(), 'Quota exceeded') || str_contains($e->getMessage(), '429')) {
                throw new \RuntimeException('Limite da API Gemini atingido. Aguarde alguns instantes e tente novamente.');
            }
            if (str_contains($e->getMessage(), 'high demand') || str_contains($e->getMessage(), 'temporarily')) {
                throw new \RuntimeException('A IA esta com alta demanda no momento. Aguarde alguns instantes e tente novamente.');
            }

            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Nao foi possivel gerar dieta com IA no momento.');
        }
    }

    private function normalizePayload(
        array $payload,
        ?string $fallbackGoal,
        mixed $fallbackKcal,
        string $fallbackStudentName
    ): array
    {
        $goal = trim((string) ($payload['goal'] ?? $fallbackGoal ?? 'Plano alimentar personalizado'));
        if ($goal === '') {
            $goal = 'Plano alimentar personalizado';
        }

        $name = trim((string) ($payload['name'] ?? ''));
        if ($name === '' || $this->isSameText($name, $fallbackStudentName)) {
            $name = $this->buildDefaultPlanName($goal, $fallbackStudentName);
        }

        $dailyKcal = $payload['daily_kcal_target'] ?? $payload['initial_kcal'] ?? $fallbackKcal;
        if ($dailyKcal !== null && $dailyKcal !== '') {
            $dailyKcal = (int) round((float) $dailyKcal);
            if ($dailyKcal < 0) {
                $dailyKcal = null;
            }
        } else {
            $dailyKcal = null;
        }

        $rawMeals = is_array($payload['meals'] ?? null) ? $payload['meals'] : [];
        $meals = [];

        foreach ($rawMeals as $index => $meal) {
            if (!is_array($meal)) {
                continue;
            }

            $mealName = trim((string) ($meal['name'] ?? 'Refeicao ' . ($index + 1)));
            if ($mealName === '') {
                $mealName = 'Refeicao ' . ($index + 1);
            }

            $time = trim((string) ($meal['time'] ?? ''));
            if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
                $time = null;
            }

            $rawFoods = is_array($meal['foods'] ?? null) ? $meal['foods'] : [];
            $foods = [];

            foreach ($rawFoods as $food) {
                if (!is_array($food)) {
                    continue;
                }

                $foodName = trim((string) ($food['name'] ?? ''));
                if ($foodName === '') {
                    continue;
                }

                $quantity = trim((string) ($food['quantity'] ?? ''));
                if ($quantity === '') {
                    $quantity = 'Porcao sugerida';
                }

                $calories = trim((string) ($food['calories'] ?? ''));
                $observation = trim((string) ($food['observation'] ?? ''));

                $foods[] = [
                    'name' => $foodName,
                    'quantity' => $quantity,
                    'calories' => $calories,
                    'observation' => $observation,
                ];
            }

            if (empty($foods)) {
                $foods[] = [
                    'name' => 'Alimento sugerido',
                    'quantity' => 'Porcao sugerida',
                    'calories' => '',
                    'observation' => '',
                ];
            }

            $meals[] = [
                'name' => $mealName,
                'time' => $time,
                'foods' => $foods,
            ];
        }

        if (empty($meals)) {
            $meals[] = [
                'name' => 'Refeicao 1',
                'time' => null,
                'foods' => [[
                    'name' => 'Alimento sugerido',
                    'quantity' => 'Porcao sugerida',
                    'calories' => '',
                    'observation' => '',
                ]],
            ];
        }

        return [
            'name' => $name,
            'goal' => $goal,
            'daily_kcal_target' => $dailyKcal,
            'meals' => $meals,
        ];
    }

    private function buildDefaultPlanName(string $goal, string $studentName): string
    {
        $goal = trim($goal);
        $studentName = trim($studentName);

        $goalNormalized = Str::lower(Str::ascii($goal));
        if ($goal !== '' && !str_contains($goalNormalized, 'plano alimentar')) {
            return 'Plano alimentar - ' . $goal;
        }

        if ($studentName !== '') {
            return 'Plano alimentar - ' . $studentName;
        }

        return 'Plano alimentar personalizado';
    }

    private function isSameText(string $left, string $right): bool
    {
        $left = trim($left);
        $right = trim($right);

        if ($left === '' || $right === '') {
            return false;
        }

        return Str::lower(Str::ascii($left)) === Str::lower(Str::ascii($right));
    }
}
