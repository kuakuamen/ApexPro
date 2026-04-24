<?php

namespace App\Http\Controllers;

use App\Models\DietPlan;
use App\Models\ProfessionalStudent;
use App\Models\User;
use App\Services\DietAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DietPlanController extends Controller
{
    private function canManageDiets(User $user): bool
    {
        return in_array($user->role, ['personal', 'nutri'], true);
    }

    private function studentBelongsToProfessional(int $professionalId, int $studentId): bool
    {
        return ProfessionalStudent::where('professional_id', $professionalId)
            ->where('student_id', $studentId)
            ->exists();
    }

    private function defaultAnamnesis(): array
    {
        return [
            'main_goal' => '',
            'weight_kg' => '',
            'height_cm' => '',
            'target_weight_kg' => '',
            'diagnosed_conditions' => [],
            'diagnosed_conditions_other' => '',
            'continuous_medication' => '',
            'food_restrictions' => [],
            'food_restrictions_other' => '',
            'food_allergies' => [],
            'food_allergies_other' => '',
            'meals_per_day' => '',
            'water_liters_per_day' => '',
            'eats_out_frequency' => '',
            'alcohol_frequency' => '',
            'disliked_foods' => '',
            'favorite_foods' => '',
            'food_style' => '',
            'food_style_other' => '',
            'training_period' => '',
            'pre_workout_meal' => '',
            'post_workout_meal' => '',
            'emotional_eating' => '',
            'diet_history' => '',
            'most_hungry_time' => '',
            'least_hungry_time' => '',
            'kcal_day' => '',
        ];
    }

    private function anamnesisRules(): array
    {
        return [
            'anamnesis' => 'nullable|array',
            'anamnesis.main_goal' => 'nullable|string|max:255',
            'anamnesis.weight_kg' => 'nullable|numeric|min:20|max:400',
            'anamnesis.height_cm' => 'nullable|numeric|min:80|max:260',
            'anamnesis.target_weight_kg' => 'nullable|numeric|min:20|max:400',
            'anamnesis.diagnosed_conditions' => 'nullable|array',
            'anamnesis.diagnosed_conditions.*' => 'string|max:100',
            'anamnesis.diagnosed_conditions_other' => 'nullable|string|max:500',
            'anamnesis.continuous_medication' => 'nullable|string|max:1000',
            'anamnesis.food_restrictions' => 'nullable|array',
            'anamnesis.food_restrictions.*' => 'string|max:100',
            'anamnesis.food_restrictions_other' => 'nullable|string|max:500',
            'anamnesis.food_allergies' => 'nullable|array',
            'anamnesis.food_allergies.*' => 'string|max:100',
            'anamnesis.food_allergies_other' => 'nullable|string|max:500',
            'anamnesis.meals_per_day' => 'nullable|numeric|min:1|max:15',
            'anamnesis.water_liters_per_day' => 'nullable|numeric|min:0|max:20',
            'anamnesis.eats_out_frequency' => 'nullable|string|max:255',
            'anamnesis.alcohol_frequency' => 'nullable|string|max:255',
            'anamnesis.disliked_foods' => 'nullable|string|max:1000',
            'anamnesis.favorite_foods' => 'nullable|string|max:1000',
            'anamnesis.food_style' => 'nullable|string|max:255',
            'anamnesis.food_style_other' => 'nullable|string|max:255',
            'anamnesis.training_period' => 'nullable|string|max:255',
            'anamnesis.pre_workout_meal' => 'nullable|string|max:255',
            'anamnesis.post_workout_meal' => 'nullable|string|max:255',
            'anamnesis.emotional_eating' => 'nullable|string|max:255',
            'anamnesis.diet_history' => 'nullable|string|max:255',
            'anamnesis.most_hungry_time' => 'nullable|date_format:H:i',
            'anamnesis.least_hungry_time' => 'nullable|date_format:H:i',
            'anamnesis.kcal_day' => 'nullable|numeric|min:600|max:10000',
        ];
    }

    private function normalizeNumericValue(mixed $value, int $decimals = 2): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (!is_numeric($value)) {
            return '';
        }

        $formatted = number_format((float) $value, $decimals, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    }

    private function normalizeHeightToCm(mixed $height): string
    {
        if ($height === null || $height === '' || !is_numeric($height)) {
            return '';
        }

        $heightValue = (float) $height;
        if ($heightValue <= 0) {
            return '';
        }

        $heightCm = $heightValue < 3.5 ? ($heightValue * 100) : $heightValue;
        return $this->normalizeNumericValue($heightCm, 1);
    }

    private function normalizeAnamnesis(array $anamnesis): array
    {
        $normalized = $this->defaultAnamnesis();

        $stringFields = [
            'main_goal',
            'diagnosed_conditions_other',
            'continuous_medication',
            'food_restrictions_other',
            'food_allergies_other',
            'eats_out_frequency',
            'alcohol_frequency',
            'disliked_foods',
            'favorite_foods',
            'food_style',
            'food_style_other',
            'training_period',
            'pre_workout_meal',
            'post_workout_meal',
            'emotional_eating',
            'diet_history',
            'most_hungry_time',
            'least_hungry_time',
        ];

        foreach ($stringFields as $field) {
            $normalized[$field] = trim((string) ($anamnesis[$field] ?? ''));
        }

        $normalized['weight_kg'] = $this->normalizeNumericValue($anamnesis['weight_kg'] ?? null, 2);
        $normalized['height_cm'] = $this->normalizeNumericValue($anamnesis['height_cm'] ?? null, 1);
        $normalized['target_weight_kg'] = $this->normalizeNumericValue($anamnesis['target_weight_kg'] ?? null, 2);
        $normalized['water_liters_per_day'] = $this->normalizeNumericValue($anamnesis['water_liters_per_day'] ?? null, 2);
        $normalized['kcal_day'] = $this->normalizeNumericValue($anamnesis['kcal_day'] ?? null, 0);
        $normalized['meals_per_day'] = $this->normalizeNumericValue($anamnesis['meals_per_day'] ?? null, 0);

        $arrayFields = [
            'diagnosed_conditions' => 'diagnosed_conditions_other',
            'food_restrictions' => 'food_restrictions_other',
            'food_allergies' => 'food_allergies_other',
        ];

        foreach ($arrayFields as $field => $otherField) {
            $rawItems = $anamnesis[$field] ?? [];
            if (!is_array($rawItems)) {
                $rawItems = [];
            }

            $items = array_values(array_unique(array_filter(
                array_map(static fn($item) => trim((string) $item), $rawItems),
                static fn($item) => $item !== ''
            )));

            if (count($items) > 1 && in_array('Nenhuma', $items, true)) {
                $items = array_values(array_filter($items, static fn($item) => $item !== 'Nenhuma'));
            }

            $normalized[$field] = $items;
            if (!in_array('Outra', $items, true)) {
                $normalized[$otherField] = '';
            }
        }

        if ($normalized['food_style'] !== 'Outro') {
            $normalized['food_style_other'] = '';
        }

        return $normalized;
    }

    private function hasMeaningfulAnamnesis(array $anamnesis): bool
    {
        foreach ($anamnesis as $value) {
            if (is_array($value) && !empty($value)) {
                return true;
            }

            if (!is_array($value) && trim((string) $value) !== '') {
                return true;
            }
        }

        return false;
    }

    private function buildStudentAnamnesisSeed(User $professional, User $student): array
    {
        $seed = $this->defaultAnamnesis();

        $lastDietPlan = DietPlan::query()
            ->where('nutritionist_id', $professional->id)
            ->where('student_id', $student->id)
            ->whereNotNull('anamnesis')
            ->latest('id')
            ->first();

        if (is_array($lastDietPlan?->anamnesis)) {
            $seed = $this->normalizeAnamnesis($lastDietPlan->anamnesis);
        }

        $latestMeasurement = $student->measurements()->latest('date')->latest('id')->first();

        if ($latestMeasurement) {
            if ($seed['weight_kg'] === '' && $latestMeasurement->weight !== null) {
                $seed['weight_kg'] = $this->normalizeNumericValue($latestMeasurement->weight, 2);
            }

            if ($seed['height_cm'] === '') {
                $heightCm = $this->normalizeHeightToCm($latestMeasurement->height);
                if ($heightCm !== '') {
                    $seed['height_cm'] = $heightCm;
                }
            }

            if ($seed['main_goal'] === '' && !empty($latestMeasurement->goal)) {
                $seed['main_goal'] = trim((string) $latestMeasurement->goal);
            }

            if ($seed['continuous_medication'] === '' && !empty($latestMeasurement->medications)) {
                $seed['continuous_medication'] = trim((string) $latestMeasurement->medications);
            }
        }

        if ($seed['continuous_medication'] === '') {
            $userMedication = trim((string) ($student->getAttribute('medications') ?? ''));
            if ($userMedication !== '') {
                $seed['continuous_medication'] = $userMedication;
            }
        }

        return $this->normalizeAnamnesis($seed);
    }

    /**
     * Lista os planos alimentares.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($this->canManageDiets($user)) {
            $diets = DietPlan::with(['student', 'nutritionist'])
                ->where('nutritionist_id', $user->id)
                ->latest()
                ->get();
        } else {
            $diets = $user->dietPlans()
                ->with('nutritionist')
                ->where('is_active', true)
                ->latest()
                ->get();
        }

        return view('diets.index', compact('diets'));
    }

    /**
     * Exibe o formulario de criacao de dieta (profissionais).
     */
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$this->canManageDiets($user)) {
            abort(403, 'Apenas profissionais podem criar dietas.');
        }

        $students = $user->students()->orderBy('name')->get();
        $canUseDietAi = $user->role === 'personal';

        $studentAnamnesisSeed = [];
        foreach ($students as $student) {
            $studentAnamnesisSeed[(string) $student->id] = $this->buildStudentAnamnesisSeed($user, $student);
        }

        $selectedStudentId = (string) old('student_id', '');
        $initialAnamnesis = old('anamnesis');

        if (!is_array($initialAnamnesis)) {
            if ($selectedStudentId !== '' && isset($studentAnamnesisSeed[$selectedStudentId])) {
                $initialAnamnesis = $studentAnamnesisSeed[$selectedStudentId];
            } else {
                $initialAnamnesis = $this->defaultAnamnesis();
            }
        }

        return view('diets.create', compact(
            'students',
            'canUseDietAi',
            'studentAnamnesisSeed',
            'initialAnamnesis'
        ));
    }

    /**
     * Salva a dieta no banco.
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$this->canManageDiets($user)) {
            abort(403);
        }

        $professionalId = $user->id;
        $studentId = (int) $request->input('student_id');
        $studentBelongsToProfessional = $this->studentBelongsToProfessional($professionalId, $studentId);

        if (!$studentBelongsToProfessional) {
            abort(403, 'Este aluno nao esta vinculado a voce.');
        }

        $validated = $request->validate(array_merge([
            'student_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string|max:255',
            'initial_kcal' => 'nullable|numeric|min:600|max:10000',
            'meals' => 'required|array|min:1',
            'meals.*.name' => 'required|string',
            'meals.*.time' => 'nullable',
            'meals.*.foods' => 'required|array|min:1',
            'meals.*.foods.*.name' => 'required|string',
            'meals.*.foods.*.quantity' => 'required|string',
            'meals.*.foods.*.calories' => 'nullable|string',
            'meals.*.foods.*.observation' => 'nullable|string',
        ], $this->anamnesisRules()));

        $anamnesis = $this->normalizeAnamnesis($validated['anamnesis'] ?? []);

        $goal = trim((string) ($validated['goal'] ?? ''));
        if ($goal === '' && $anamnesis['main_goal'] !== '') {
            $goal = $anamnesis['main_goal'];
        }

        if (($validated['initial_kcal'] ?? null) !== null && $anamnesis['kcal_day'] === '') {
            $anamnesis['kcal_day'] = $this->normalizeNumericValue($validated['initial_kcal'], 0);
        }

        if ($goal !== '') {
            $anamnesis['main_goal'] = $goal;
        }

        $plan = DietPlan::create([
            'student_id' => $validated['student_id'],
            'nutritionist_id' => $user->id,
            'name' => $validated['name'],
            'goal' => $goal !== '' ? $goal : null,
            'anamnesis' => $this->hasMeaningfulAnamnesis($anamnesis) ? $anamnesis : null,
            'start_date' => now(),
            'is_active' => true,
        ]);

        foreach ($validated['meals'] as $mealIndex => $mealData) {
            $meal = $plan->meals()->create([
                'name' => $mealData['name'],
                'time' => $mealData['time'] ?? null,
                'order' => $mealIndex,
            ]);

            foreach ($mealData['foods'] as $foodData) {
                $meal->foods()->create([
                    'name' => $foodData['name'],
                    'quantity' => $foodData['quantity'],
                    'calories' => $foodData['calories'] ?? null,
                    'observation' => $foodData['observation'] ?? null,
                ]);
            }
        }

        return redirect()->route('diets.index')->with('success', 'Plano alimentar criado com sucesso!');
    }

    /**
     * Exibe uma dieta especifica.
     */
    public function show(DietPlan $diet)
    {
        if (Auth::id() !== $diet->student_id && Auth::id() !== $diet->nutritionist_id) {
            abort(403);
        }

        $diet->load('meals.foods');

        return view('diets.show', compact('diet'));
    }

    public function generateWithAi(Request $request, DietAiService $dietAiService): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'personal') {
            return response()->json([
                'message' => 'Geracao de dieta com IA disponivel apenas para personal.',
            ], 403);
        }

        $validated = $request->validate(array_merge([
            'student_id' => 'required|exists:users,id',
            'goal' => 'nullable|string|max:255',
            'initial_kcal' => 'nullable|numeric|min:600|max:10000',
        ], $this->anamnesisRules()));

        $studentId = (int) $validated['student_id'];
        if (!$this->studentBelongsToProfessional($user->id, $studentId)) {
            return response()->json([
                'message' => 'Este aluno nao esta vinculado a voce.',
            ], 403);
        }

        $anamnesis = $this->normalizeAnamnesis($validated['anamnesis'] ?? []);

        $goal = trim((string) ($validated['goal'] ?? ''));
        if ($goal === '' && $anamnesis['main_goal'] !== '') {
            $goal = $anamnesis['main_goal'];
        }

        $initialKcal = $validated['initial_kcal'] ?? null;
        if ($initialKcal === null && $anamnesis['kcal_day'] !== '' && is_numeric($anamnesis['kcal_day'])) {
            $initialKcal = (float) $anamnesis['kcal_day'];
        }

        if ($initialKcal !== null) {
            $anamnesis['kcal_day'] = $this->normalizeNumericValue($initialKcal, 0);
        }

        if ($goal !== '') {
            $anamnesis['main_goal'] = $goal;
        }

        $student = User::query()->findOrFail($studentId);
        $latestMeasurement = $student->measurements()->latest('date')->latest('id')->first();

        $studentData = [
            'student_name' => $student->name,
            'age' => $student->birth_date?->age,
            'gender' => $student->gender,
            'goal' => $goal !== '' ? $goal : null,
            'initial_kcal' => $initialKcal,
            'weight' => $latestMeasurement?->weight,
            'height' => $latestMeasurement?->height,
            'body_fat' => $latestMeasurement?->body_fat,
            'anamnesis' => $this->hasMeaningfulAnamnesis($anamnesis) ? $anamnesis : null,
        ];

        try {
            $generatedDiet = $dietAiService->generateDiet($studentData);
            return response()->json($generatedDiet);
        } catch (\Throwable $e) {
            Log::error('Erro ao gerar dieta com IA', [
                'user_id' => $user->id,
                'student_id' => $studentId,
                'error' => $e->getMessage(),
            ]);

            $message = $e instanceof \RuntimeException
                ? $e->getMessage()
                : 'Nao foi possivel gerar a dieta com IA no momento.';

            return response()->json(['message' => $message], 422);
        }
    }
}

