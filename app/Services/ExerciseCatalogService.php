<?php

namespace App\Services;

use App\Models\Exercise;
use Illuminate\Support\Str;

class ExerciseCatalogService
{
    private ?array $catalogNames = null;
    private ?array $normalizedMap = null;
    private ?array $mediaMap = null;
    private ?array $dbMediaRows = null;

    public function getCatalogNames(): array
    {
        if ($this->catalogNames !== null) {
            return $this->catalogNames;
        }

        $names = [];

        // From JSON map (name -> media), but canonicalized by DB names when possible.
        foreach (array_keys($this->getMediaMap()) as $normalizedName) {
            $canonical = $this->findCanonicalFromDbByNormalizedName($normalizedName);
            if ($canonical !== null) {
                $names[] = $canonical;
            }
        }

        // Also include DB entries that already have gifdotreino media.
        foreach ($this->getDbMediaRows() as $row) {
            if (!empty($row['name'])) {
                $names[] = $row['name'];
            }
        }

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
            return 'CATALOGO INDISPONIVEL: sem exercicios com midia valida.';
        }

        $list = implode('; ', $names);

        return 'REGRA OBRIGATORIA DE CATALOGO FECHADO: '
            . 'Voce deve escolher exercicios SOMENTE desta lista oficial. '
            . 'Nao invente nomes e nao use sinonimos fora da lista. '
            . "Lista oficial ({$this->count()} exercicios com midia): {$list}.";
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

    public function getMediaUrlForName(string $name): ?string
    {
        $canonical = $this->canonicalize($name);
        if ($canonical === null) {
            return null;
        }

        $normalized = $this->normalize($canonical);
        $mediaMap = $this->getMediaMap();

        if (!empty($mediaMap[$normalized])) {
            return $mediaMap[$normalized];
        }

        foreach ($this->getDbMediaRows() as $row) {
            if ($row['normalized_name'] === $normalized && !empty($row['video_url'])) {
                return $row['video_url'];
            }
        }

        return null;
    }

    public function resolveCatalogExerciseOrFail(string $name): array
    {
        $canonical = $this->canonicalizeOrFail($name);
        $mediaUrl = $this->getMediaUrlForName($canonical);

        if (!$mediaUrl) {
            throw new \RuntimeException("Exercicio sem midia mapeada no catalogo: {$canonical}");
        }

        return [
            'name' => $canonical,
            'media_url' => $mediaUrl,
        ];
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

                $resolved = $this->resolveCatalogExerciseOrFail($name);
                $analysis['workout_recommendation']['days'][$dayIndex]['exercises'][$exerciseIndex]['name'] = $resolved['name'];
                $analysis['workout_recommendation']['days'][$dayIndex]['exercises'][$exerciseIndex]['video_url'] = $resolved['media_url'];
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

    private function getMediaMap(): array
    {
        if ($this->mediaMap !== null) {
            return $this->mediaMap;
        }

        $map = [];
        $jsonPath = storage_path('app/gifdotreino_catalog.json');

        if (is_file($jsonPath)) {
            $raw = @file_get_contents($jsonPath);
            if ($raw !== false) {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->extractMediaMapFromArray($decoded, $map);
                }
            }
        }

        $this->mediaMap = $map;
        return $this->mediaMap;
    }

    private function extractMediaMapFromArray(array $data, array &$map): void
    {
        $walker = function ($value) use (&$walker, &$map) {
            if (!is_array($value)) {
                return;
            }

            $possibleNameKeys = ['name', 'nome', 'title', 'exercise', 'exercise_name', 'slug'];
            $possibleUrlKeys = ['gif_url', 'gifUrl', 'gif', 'url', 'media_url', 'video_url', 'path', 'file', 'media'];

            $candidateName = null;
            foreach ($possibleNameKeys as $key) {
                if (isset($value[$key]) && is_string($value[$key]) && trim($value[$key]) !== '') {
                    $candidateName = trim($value[$key]);
                    break;
                }
            }

            $candidateUrl = null;
            foreach ($possibleUrlKeys as $key) {
                if (isset($value[$key]) && is_string($value[$key]) && trim($value[$key]) !== '') {
                    $candidateUrl = trim($value[$key]);
                    break;
                }
            }

            if ($candidateName && $candidateUrl) {
                $normalizedName = $this->normalize($candidateName);
                $normalizedUrl = $this->normalizeMediaUrl($candidateUrl);
                if ($normalizedName !== '' && $normalizedUrl !== '') {
                    $map[$normalizedName] = $normalizedUrl;
                }
            }

            foreach ($value as $item) {
                $walker($item);
            }
        };

        $walker($data);
    }

    private function normalizeMediaUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        if (str_starts_with($url, '//')) {
            return 'https:' . $url;
        }

        if (str_starts_with($url, '/')) {
            $appUrl = rtrim((string) config('app.url'), '/');
            return $appUrl !== '' ? $appUrl . $url : $url;
        }

        return $url;
    }

    private function findCanonicalFromDbByNormalizedName(string $normalizedName): ?string
    {
        foreach ($this->getDbMediaRows() as $row) {
            if ($row['normalized_name'] === $normalizedName) {
                return $row['name'];
            }
        }

        return null;
    }

    private function getDbMediaRows(): array
    {
        if ($this->dbMediaRows !== null) {
            return $this->dbMediaRows;
        }

        $rows = Exercise::query()
            ->whereNotNull('name')
            ->whereNotNull('video_url')
            ->where(function ($q) {
                $q->where('video_url', 'like', '%/media/gifdotreino/%')
                    ->orWhere('video_url', 'like', '%gifdotreino.com%');
            })
            ->orderByDesc('id')
            ->get(['name', 'video_url']);

        $result = [];
        foreach ($rows as $row) {
            $name = trim((string) $row->name);
            $videoUrl = trim((string) $row->video_url);
            if ($name === '' || $videoUrl === '') {
                continue;
            }
            $result[] = [
                'name' => $name,
                'normalized_name' => $this->normalize($name),
                'video_url' => $videoUrl,
            ];
        }

        $this->dbMediaRows = $result;
        return $this->dbMediaRows;
    }

    private function normalize(string $text): string
    {
        $text = Str::of($text)->ascii()->lower()->toString();
        $text = preg_replace('/[^a-z0-9]+/i', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        return trim($text);
    }
}
