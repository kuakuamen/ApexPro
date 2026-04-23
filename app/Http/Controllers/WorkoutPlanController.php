<?php

namespace App\Http\Controllers;

use App\Models\WorkoutPlan;
use App\Models\User;
use App\Models\ProfessionalStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WorkoutLog;
use App\Services\ExerciseCatalogService;

class WorkoutPlanController extends Controller
{
    protected ExerciseCatalogService $exerciseCatalog;

    public function __construct(ExerciseCatalogService $exerciseCatalog)
    {
        $this->exerciseCatalog = $exerciseCatalog;
    }

    /**
     * Alterna o status de conclusão de um exercício (AJAX).
     */
    public function toggleExercise(Request $request, $exerciseId)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            // Validar se usuário está autenticado
            if (!$user) {
                return response()->json(['error' => 'Não autenticado'], 401);
            }
            
            // Validar se é aluno
            if ($user->role !== 'aluno') {
                return response()->json(['error' => 'Acesso negado - apenas alunos'], 403);
            }
            
            // Validar se exercício existe
            $exercise = \App\Models\Exercise::find($exerciseId);
            if (!$exercise) {
                return response()->json(['error' => 'Exercício não encontrado'], 404);
            }
            
            $date = now()->format('Y-m-d');

            // Verificar se já existe log para hoje
            $log = WorkoutLog::where('student_id', $user->id)
                ->where('exercise_id', $exerciseId)
                ->where('date', $date)
                ->first();

            if ($log) {
                // Se existe, apaga (desmarca)
                $log->delete();
                return response()->json(['status' => 'uncompleted', 'message' => 'Exercício desmarcado']);
            } else {
                // Se não existe, cria (marca)
                WorkoutLog::create([
                    'student_id' => $user->id,
                    'exercise_id' => $exerciseId,
                    'date' => $date,
                    'completed_at' => now(),
                ]);
                return response()->json(['status' => 'completed', 'message' => 'Exercício marcado']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao processing: ' . $e->getMessage()], 500);
        }
    }

    public function exerciseYoutubeVideo(Request $request)
    {
        if (Auth::user()?->role !== 'aluno') {
            abort(403);
        }

        $exerciseName = $this->cleanExerciseName(trim((string) $request->query('name')));

        if ($exerciseName === '') {
            return response()->json(['message' => 'Nome do exercicio nao informado.'], 422);
        }

        $apiKey = (string) config('services.youtube.api_key');

        if (!$apiKey) {
            return response()->json(['message' => 'Busca de videos indisponivel: chave do YouTube nao configurada.'], 503);
        }

        $cacheKey = 'youtube_exercise_video_v6_' . md5(mb_strtolower($exerciseName));

        $video = Cache::get($cacheKey);

        if (!$video) {
            try {
                $video = $this->searchYoutubeExerciseVideo($apiKey, $exerciseName);

                if ($video) {
                    Cache::put($cacheKey, $video, now()->addYear());
                }
            } catch (\Throwable $e) {
                Log::warning('YouTube exercise video lookup failed', [
                    'exercise' => $exerciseName,
                    'error' => $e->getMessage(),
                ]);

                return response()->json(['message' => 'Nao foi possivel consultar o YouTube agora. Tente novamente em instantes.'], 503);
            }
        }

        if (!$video) {
            return response()->json(['message' => 'Nao encontrei um video para este exercicio.'], 404);
        }

        return response()->json($video);
    }

    private function searchYoutubeExerciseVideo(string $apiKey, string $exerciseName): ?array
    {
        $queries = $this->youtubeQueryCandidates($exerciseName);

        $item = null;

        foreach ($queries as $query) {
            $response = Http::timeout(8)->get('https://www.googleapis.com/youtube/v3/search', [
                'part' => 'snippet',
                'q' => $query,
                'key' => $apiKey,
                'type' => 'video',
                'videoEmbeddable' => 'true',
                'videoDuration' => 'short',
                'safeSearch' => 'strict',
                'maxResults' => 20,
                'relevanceLanguage' => 'pt',
            ]);

            if (!$response->successful()) {
                throw new \RuntimeException('YouTube search API returned HTTP ' . $response->status());
            }

            $items = collect($response->json('items', []))
                ->filter(fn ($video) => $this->youtubeResultMatchesExercise($exerciseName, $video))
                ->values();

            if ($items->isEmpty()) {
                continue;
            }

            $durations = $this->youtubeVideoDurations($apiKey, $items->pluck('id.videoId')->filter()->values()->all());

            $items = $items->map(function ($video) {
                $text = mb_strtolower(
                    ($video['snippet']['title'] ?? '') . ' ' . ($video['snippet']['description'] ?? '')
                );

                $bonus = 0;
                if (str_contains($text, 'gif') || str_contains($text, 'animacao') || str_contains($text, 'animação')) {
                    $bonus += 2;
                }
                if (str_contains($text, '#shorts') || str_contains($text, 'shorts')) {
                    $bonus += 1;
                }

                $video['_bonus_score'] = $bonus;
                return $video;
            })->sortByDesc('_bonus_score')->values();

            $item = $items->first(function ($video) use ($durations) {
                $videoId = $video['id']['videoId'] ?? null;
                return $videoId && isset($durations[$videoId]) && $durations[$videoId] <= 30;
            });

            if ($item) {
                break;
            }
        }

        $videoId = $item['id']['videoId'] ?? null;

        if (!$videoId) {
            return null;
        }

        return [
            'video_id' => $videoId,
            'title' => $item['snippet']['title'] ?? $exerciseName,
            'channel_title' => $item['snippet']['channelTitle'] ?? null,
        ];
    }

    private function youtubeResultMatchesExercise(string $exerciseName, array $item): bool
    {
        $text = $this->normalizeExerciseText(($item['snippet']['title'] ?? '') . ' ' . ($item['snippet']['description'] ?? ''));
        $exercise = $this->normalizeExerciseText($exerciseName);

        $tokens = collect(explode(' ', $exercise))
            ->reject(fn ($term) => mb_strlen($term) < 4 || in_array($term, ['para', 'parede', 'porta', 'polia', 'corda', 'com', 'exercicio', 'execucao', 'musculacao'], true))
            ->values();

        if ($tokens->isNotEmpty()) {
            $matched = $tokens->filter(fn ($term) => str_contains($text, $term))->count();
            $required = $tokens->count() >= 2 ? 2 : 1;
            if ($matched < $required) {
                return false;
            }
        }

        if (str_contains($exercise, 'deitada') && !str_contains($text, 'deitad')) {
            return false;
        }

        if ((str_contains($exercise, 'crucifixo') || str_contains($exercise, 'fly'))
            && (str_contains($text, 'supino') || str_contains($text, 'press'))) {
            return false;
        }

        $conflicts = [
            'reto' => ['inclinado', 'declinado'],
            'inclinado' => ['reto', 'declinado'],
            'declinado' => ['reto', 'inclinado'],
            'barra' => ['halter', 'halteres', 'dumbbell'],
            'halteres' => ['barra', 'barbell'],
            'halter' => ['barra', 'barbell'],
        ];

        foreach ($conflicts as $term => $blockedTerms) {
            if (str_contains($exercise, $term)) {
                foreach ($blockedTerms as $blockedTerm) {
                    if (str_contains($text, $blockedTerm)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function youtubeQueryCandidates(string $exerciseName): array
    {
        $base = trim($exerciseName);
        $normalized = $this->normalizeExerciseText($base);

        $aliases = [
            'flexora deitada' => 'mesa flexora deitada',
            'cadeira flexora' => 'mesa flexora',
            'elevacao pelvica com barra' => 'hip thrust com barra',
            'gluteo na maquina' => 'gluteo maquina',
            'crucifixo inclinado com halteres' => 'crucifixo inclinado halteres',
            'desenvolvimento com halteres sentado' => 'desenvolvimento sentado halteres',
        ];

        $preferred = $base;
        foreach ($aliases as $pt => $replacement) {
            if (str_contains($normalized, $pt)) {
                $preferred = $replacement;
                break;
            }
        }

        return [
            $preferred . ' gif shorts execução exercício musculação',
            $preferred . ' execução exercício musculação',
            $base . ' gif shorts',
            $base . ' execução exercício musculação',
        ];
    }

    private function youtubeVideoDurations(string $apiKey, array $videoIds): array
    {
        if ($videoIds === []) {
            return [];
        }

        $response = Http::timeout(8)->get('https://www.googleapis.com/youtube/v3/videos', [
            'part' => 'contentDetails',
            'id' => implode(',', $videoIds),
            'key' => $apiKey,
        ]);

        if (!$response->successful()) {
            return [];
        }

        return collect($response->json('items', []))
            ->mapWithKeys(function ($item) {
                return [
                    $item['id'] => $this->youtubeDurationToSeconds($item['contentDetails']['duration'] ?? ''),
                ];
            })
            ->filter(fn ($seconds) => $seconds !== null)
            ->all();
    }

    private function youtubeDurationToSeconds(string $duration): ?int
    {
        if (!preg_match('/^PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?$/', $duration, $matches)) {
            return null;
        }

        return ((int) ($matches[1] ?? 0) * 3600)
            + ((int) ($matches[2] ?? 0) * 60)
            + (int) ($matches[3] ?? 0);
    }

    private function normalizeExerciseText(string $text): string
    {
        $text = mb_strtolower($text);
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        $text = preg_replace('/[^a-z0-9]+/', ' ', $text) ?? $text;

        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }

    /**
     * Remove termos em inglês do nome do exercício antes de buscar no YouTube.
     * Ex: "Puxada Alta (Lat Pulldown) com Barra" → "Puxada Alta com Barra"
     */
    private function cleanExerciseName(string $name): string
    {
        // Remove conteúdo em parênteses que contém letras maiúsculas ou palavras em inglês
        $name = preg_replace('/\s*\([^)]*[A-Z][^)]*\)/', '', $name) ?? $name;

        // Remove termos técnicos em inglês comuns que a IA coloca
        $englishTerms = [
            'Lat Pulldown', 'Pull Down', 'Pulldown', 'Pull-down',
            'Deadlift', 'Squat', 'Bench Press', 'Overhead Press',
            'Romanian', 'Bulgarian', 'Goblet', 'Cable', 'Barbell',
            'Dumbbell', 'Leg Press', 'Leg Curl', 'Leg Extension',
            'Hip Thrust', 'Plank', 'Push Up', 'Push-Up', 'Pushup',
            'Pull Up', 'Pull-Up', 'Pullup', 'Chin Up', 'Chin-Up',
            'Crunch', 'Lunge', 'Row', 'Fly', 'Flye', 'Curl',
            'Press', 'Raise', 'Extension', 'Kickback', 'Shrug',
        ];

        foreach ($englishTerms as $term) {
            $name = preg_replace('/\b' . preg_quote($term, '/') . '\b/i', '', $name) ?? $name;
        }

        // Limpa espaços extras
        return trim(preg_replace('/\s+/', ' ', $name) ?? $name);
    }

    /**
     * Lista os treinos disponíveis.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        if ($user->role === 'personal') {
            // Personal vê os treinos que criou para seus alunos
            $workouts = $user->students()
                ->with('workoutPlans')
                ->get()
                ->pluck('workoutPlans')
                ->flatten()
                ->sortByDesc('created_at');
        } else {
            // Aluno vê seus próprios treinos ativos
            $workouts = $user->workoutPlans()
                ->where('is_active', true)
                ->latest()
                ->get();
        }

        return view('workouts.index', compact('workouts'));
    }

    /**
     * Exibe o formulário de criação de treino (Apenas Personal).
     */
    public function create(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'personal') {
            abort(403, 'Apenas personal trainers podem criar treinos.');
        }

        // Busca apenas os alunos vinculados a este personal
        $students = $user->students()->get();
        $catalogExercises = $this->exerciseCatalog->getCatalogItems();
        
        // Se vier student_id na URL, pré-seleciona
        $selectedStudentId = $request->query('student_id');

        return view('workouts.create', compact('students', 'selectedStudentId', 'catalogExercises'));
    }

    /**
     * Salva o treino no banco de dados.
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'personal') {
            abort(403);
        }

        // Validar que student_id existe E pertence ao personal autenticado
        $personalId = $user->id;
        $studentId = $request->input('student_id');
        $studentBelongsToPersonal = ProfessionalStudent::where('professional_id', $personalId)
            ->where('student_id', $studentId)
            ->exists();
        
        if (!$studentBelongsToPersonal) {
            abort(403, 'Este aluno não está vinculado a você.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string|max:255',
            'days' => 'required|array|min:1',
            'days.*.name' => 'required|string',
            'days.*.exercises' => 'required|array|min:1',
            'days.*.exercises.*.name' => 'required|string',
            'days.*.exercises.*.sets' => 'nullable|string',
            'days.*.exercises.*.reps' => 'nullable|string',
            'days.*.exercises.*.rest_time' => 'nullable|string',
            'days.*.exercises.*.video_url' => 'nullable|url|max:500',
        ]);

        try {
            // Inativar treinos anteriores do aluno
            WorkoutPlan::where('student_id', $validated['student_id'])
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Criar o Plano
            $plan = WorkoutPlan::create([
                'student_id' => $validated['student_id'],
                'personal_id' => Auth::id(),
                'name' => $validated['name'],
                'goal' => $validated['goal'],
                'start_date' => now(),
                'is_active' => true,
            ]);

            // Criar Dias e Exercícios (somente catálogo com mídia)
            foreach ($validated['days'] as $dayIndex => $dayData) {
                $day = $plan->days()->create([
                    'name' => $dayData['name'],
                    'order' => $dayIndex,
                ]);

                foreach ($dayData['exercises'] as $exerciseIndex => $exerciseData) {
                    $isCustomExercise = !empty($exerciseData['custom_exercise']);
                    if ($isCustomExercise) {
                        $customName = trim((string) ($exerciseData['name'] ?? ''));
                        if ($customName === '') {
                            throw new \RuntimeException('Exercicio personalizado sem nome.');
                        }
                        $resolvedExercise = [
                            'name' => $customName,
                            'media_url' => null,
                        ];
                    } else {
                        $resolvedExercise = $this->exerciseCatalog->resolveCatalogExerciseOrFail((string) $exerciseData['name']);
                    }

                    $day->exercises()->create([
                        'name' => $resolvedExercise['name'],
                        'sets' => $exerciseData['sets'] ?? null,
                        'reps' => $exerciseData['reps'] ?? null,
                        'rest_time' => $exerciseData['rest_time'] ?? null,
                        'video_url' => $resolvedExercise['media_url'],
                        'order' => $exerciseIndex,
                    ]);
                }
            }
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['days' => $e->getMessage()]);
        }

        return redirect()->route('workouts.index')->with('success', 'Treino criado com sucesso!');
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(WorkoutPlan $workout)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'personal' || Auth::id() !== $workout->personal_id) {
            abort(403);
        }

        $workout->load('days.exercises');
        $students = $user->students()->get();
        $catalogExercises = $this->exerciseCatalog->getCatalogItems();

        return view('workouts.edit', compact('workout', 'students', 'catalogExercises'));
    }

    /**
     * Atualiza o treino existente.
     */
    public function update(Request $request, WorkoutPlan $workout)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'personal' || $user->id !== $workout->personal_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string|max:255',
            'days' => 'required|array|min:1',
            'days.*.name' => 'required|string',
            'days.*.exercises' => 'required|array|min:1',
            'days.*.exercises.*.name' => 'required|string',
            'days.*.exercises.*.sets' => 'nullable|string',
            'days.*.exercises.*.reps' => 'nullable|string',
            'days.*.exercises.*.rest_time' => 'nullable|string',
            'days.*.exercises.*.video_url' => 'nullable|url|max:500',
        ]);

        try {
            // Atualizar dados básicos
            $workout->update([
                'name' => $validated['name'],
                'goal' => $validated['goal'],
            ]);

            // Recria estrutura de dias/exercícios
            $workout->days()->delete(); // Cascade deve apagar exercises

            foreach ($validated['days'] as $dayIndex => $dayData) {
                $day = $workout->days()->create([
                    'name' => $dayData['name'],
                    'order' => $dayIndex,
                ]);

                foreach ($dayData['exercises'] as $exerciseIndex => $exerciseData) {
                    $isCustomExercise = !empty($exerciseData['custom_exercise']);
                    if ($isCustomExercise) {
                        $customName = trim((string) ($exerciseData['name'] ?? ''));
                        if ($customName === '') {
                            throw new \RuntimeException('Exercicio personalizado sem nome.');
                        }
                        $resolvedExercise = [
                            'name' => $customName,
                            'media_url' => null,
                        ];
                    } else {
                        $resolvedExercise = $this->exerciseCatalog->resolveCatalogExerciseOrFail((string) $exerciseData['name']);
                    }

                    $day->exercises()->create([
                        'name' => $resolvedExercise['name'],
                        'sets' => $exerciseData['sets'] ?? null,
                        'reps' => $exerciseData['reps'] ?? null,
                        'rest_time' => $exerciseData['rest_time'] ?? null,
                        'video_url' => $resolvedExercise['media_url'],
                        'order' => $exerciseIndex,
                    ]);
                }
            }
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['days' => $e->getMessage()]);
        }

        return redirect()->route('workouts.show', $workout)->with('success', 'Treino atualizado com sucesso!');
    }

    /**
     * Exibe um treino específico.
     */
    public function toggleActive(WorkoutPlan $workout)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'personal' || $workout->personal_id !== $user->id) {
            abort(403);
        }

        if (!$workout->is_active) {
            // Ativando: inativa todos os outros treinos do aluno primeiro
            WorkoutPlan::where('student_id', $workout->student_id)
                ->where('id', '!=', $workout->id)
                ->update(['is_active' => false]);

            $workout->update(['is_active' => true]);
            $msg = 'Treino ativado. Os outros treinos foram inativados.';
        } else {
            // Inativando
            $workout->update(['is_active' => false]);
            $msg = 'Treino inativado.';
        }

        return redirect()->route('personal.students.show', $workout->student_id)
            ->with('success', $msg);
    }

    public function destroy(WorkoutPlan $workout)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'personal' || $workout->personal_id !== $user->id) {
            abort(403);
        }

        $studentId = $workout->student_id;
        $workout->days()->each(fn($day) => $day->exercises()->delete());
        $workout->days()->delete();
        $workout->delete();

        return redirect()->route('personal.students.show', $studentId)
            ->with('success', 'Treino excluído com sucesso.');
    }

    public function show(WorkoutPlan $workout)
    {
        // Verificar permissão
        if (Auth::id() !== $workout->student_id && Auth::id() !== $workout->personal_id) {
            abort(403);
        }

        $workout->load('days.exercises');

        // Carregar logs de hoje do aluno
        $todayLogs = WorkoutLog::where('student_id', Auth::id())
            ->where('date', now()->format('Y-m-d'))
            ->pluck('exercise_id')
            ->toArray();

        return view('workouts.show', compact('workout', 'todayLogs'));
    }
}
