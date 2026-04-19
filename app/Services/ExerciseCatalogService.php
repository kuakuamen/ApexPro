<?php

namespace App\Services;

use App\Models\Exercise;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExerciseCatalogService
{
    private ?array $catalogNames = null;
    private ?array $normalizedMap = null;

    public function getCatalogNames(): array
    {
        if ($this->catalogNames !== null) {
            return $this->catalogNames;
        }

        $names = [];

        $jsonPath = storage_path('app/gifdotreino_catalog.json');
        if (is_file($jsonPath)) {
            $raw = @file_get_contents($jsonPath);
            if ($raw !== false) {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $names = array_merge($names, $this->extractNamesFromArray($decoded));
                }
            }
        }

        $dbNames = Exercise::query()
            ->whereNotNull('video_url')
            ->where(function ($q) {
                $q->where('video_url', 'like', '%/media/gifdotreino/%')
                    ->orWhere('video_url', 'like', '%gifdotreino.com%');
            })
            ->whereNotNull('name')
            ->pluck('name')
            ->all();

        $names = array_merge($names, $dbNames);

        $names = array_values(array_unique(array_filter(array_map(
            static fn($n) => trim((string) $n),
            $names
        ))));

        sort($names, SORT_NATURAL | SORT_FLAG_CASE);

        $this->catalogNames = $names;
        return $this->catalogNames;
    }

    public function hasCatalog(): bool
    {
        return !empty($this->getCatalogNames());
    }

    public function buildPromptRestriction(): string
    {
        $names = $this->getCatalogNames();

        if (empty($names)) {
            return "CATALOGO INDISPONIVEL: se nao houver catalogo carregado, interrompa e retorne erro estruturado em JSON.";
        }

        $list = implode('; ', $names);

        return "REGRA OBRIGATORIA DE CATALOGO FECHADO: "
            . "Voce deve escolher os exercicios SOMENTE desta lista oficial. "
            . "Nao invente nomes, nao use sinonimos fora da lista, nao traduza para ingles. "
            . "Lista oficial ({$this->count()} exercicios): {$list}.";
    }

    public function canonicalize(string $name): ?string
    {
        $name = trim($name);
        if ($name === '') {
            return null;
        }

        $map = $this->getNormalizedMap();
        if (empty($map)) {
            return null;
        }

        $normalized = $this->normalize($name);
        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        $best = null;
        $bestDistance = PHP_INT_MAX;
        foreach ($map as $candidateNorm => $candidateCanonical) {
            if (str_contains($candidateNorm, $normalized) || str_contains($normalized, $candidateNorm)) {
                return $candidateCanonical;
            }

            $distance = levenshtein($normalized, $candidateNorm);
            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = $candidateCanonical;
            }
        }

        $maxAllowed = max(2, (int) floor(strlen($normalized) * 0.30));
        if ($best !== null && $bestDistance <= $maxAllowed) {
            return $best;
        }

        return null;
    }

    public function canonicalizeOrFail(string $name): string
    {
        $canonical = $this->canonicalize($name);
        if ($canonical === null) {
            throw new \RuntimeException("Exercicio fora do catalogo: {$name}");
        }

        return $canonical;
    }

    public function enforceWorkoutCatalog(array $analysis): array
    {
        if (!$this->hasCatalog()) {
            throw new \RuntimeException('Catalogo de exercicios indisponivel para validacao.');
        }

        $days = data_get($analysis, 'workout_recommendation.days', []);
        if (!is_array($days)) {
            throw new \RuntimeException('Resposta da IA invalida: days ausente.');
        }

        foreach ($days as $dayIndex => $day) {
            $exercises = $day['exercises'] ?? [];
            if (!is_array($exercises)) {
                continue;
            }

            foreach ($exercises as $exerciseIndex => $exercise) {
                $name = trim((string) ($exercise['name'] ?? ''));
                if ($name === '') {
                    throw new \RuntimeException('Resposta da IA invalida: exercicio sem nome.');
                }

                $canonical = $this->canonicalize($name);
                if ($canonical === null) {
                    throw new \RuntimeException("A IA retornou exercicio fora do catalogo: {$name}");
                }

                $analysis['workout_recommendation']['days'][$dayIndex]['exercises'][$exerciseIndex]['name'] = $canonical;
            }
        }

        return $analysis;
    }

    public function count(): int
    {
        return count($this->getCatalogNames());
    }

    private function getNormalizedMap(): array
    {
        if ($this->normalizedMap !== null) {
            return $this->normalizedMap;
        }

        $map = [];
        foreach ($this->getCatalogNames() as $name) {
            $map[$this->normalize($name)] = $name;
        }

        $this->normalizedMap = $map;
        return $this->normalizedMap;
    }

    private function normalize(string $text): string
    {
        $text = Str::of($text)->ascii()->lower()->toString();
        $text = preg_replace('/[^a-z0-9]+/i', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        return trim($text);
    }

    private function extractNamesFromArray(array $data): array
    {
        $names = [];

        $walker = function ($value) use (&$walker, &$names) {
            if (is_array($value)) {
                $possibleKeys = ['name', 'nome', 'title', 'exercise', 'exercise_name'];
                foreach ($possibleKeys as $key) {
                    if (isset($value[$key]) && is_string($value[$key])) {
                        $candidate = trim($value[$key]);
                        if ($candidate !== '') {
                            $names[] = $candidate;
                        }
                    }
                }

                foreach ($value as $item) {
                    $walker($item);
                }
            }
        };

        $walker($data);

        if (empty($names)) {
            Log::warning('ExerciseCatalogService: nenhum nome extraido do JSON do catalogo.');
        }

        return $names;
    }
}
