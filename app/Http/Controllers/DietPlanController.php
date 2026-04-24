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

    /**
     * Lista os planos alimentares.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($this->canManageDiets($user)) {
            // Profissional (personal/nutri legado) ve dietas criadas por ele.
            $diets = DietPlan::with(['student', 'nutritionist'])
                ->where('nutritionist_id', $user->id)
                ->latest()
                ->get();
        } else {
            // Aluno ve suas dietas ativas.
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

        // Busca apenas os alunos vinculados a este profissional.
        $students = $user->students()->orderBy('name')->get();
        $canUseDietAi = $user->role === 'personal';

        return view('diets.create', compact('students', 'canUseDietAi'));
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

        // Validar que student_id existe e pertence ao profissional autenticado.
        $professionalId = $user->id;
        $studentId = $request->input('student_id');
        $studentBelongsToProfessional = $this->studentBelongsToProfessional($professionalId, (int) $studentId);

        if (!$studentBelongsToProfessional) {
            abort(403, 'Este aluno nao esta vinculado a voce.');
        }

        $validated = $request->validate([
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
        ]);

        // Criar o plano.
        $plan = DietPlan::create([
            'student_id' => $validated['student_id'],
            'nutritionist_id' => $user->id,
            'name' => $validated['name'],
            'goal' => $validated['goal'],
            'start_date' => now(),
            'is_active' => true,
        ]);

        // Criar refeicoes e alimentos.
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
        // Verificar permissao.
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

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'goal' => 'nullable|string|max:255',
            'initial_kcal' => 'nullable|numeric|min:600|max:10000',
        ]);

        $studentId = (int) $validated['student_id'];
        if (!$this->studentBelongsToProfessional($user->id, $studentId)) {
            return response()->json([
                'message' => 'Este aluno nao esta vinculado a voce.',
            ], 403);
        }

        $student = User::query()->findOrFail($studentId);
        $latestMeasurement = $student->measurements()->latest('date')->latest('id')->first();

        $studentData = [
            'student_name' => $student->name,
            'age' => $student->birth_date?->age,
            'gender' => $student->gender,
            'goal' => $validated['goal'] ?? null,
            'initial_kcal' => $validated['initial_kcal'] ?? null,
            'weight' => $latestMeasurement?->weight,
            'height' => $latestMeasurement?->height,
            'body_fat' => $latestMeasurement?->body_fat,
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
