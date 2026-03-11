<?php

namespace App\Http\Controllers;

use App\Models\DietPlan;
use App\Models\User;
use App\Models\ProfessionalStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DietPlanController extends Controller
{
    /**
     * Lista os planos alimentares.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        if ($user->role === 'nutri') {
            // Nutri vê as dietas que criou para seus alunos
            $diets = $user->students()
                ->with('dietPlans')
                ->get()
                ->pluck('dietPlans')
                ->flatten()
                ->sortByDesc('created_at');
        } else {
            // Aluno vê suas dietas ativas
            $diets = $user->dietPlans()
                ->where('is_active', true)
                ->latest()
                ->get();
        }

        return view('diets.index', compact('diets'));
    }

    /**
     * Exibe o formulário de criação de dieta (Apenas Nutri).
     */
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'nutri') {
            abort(403, 'Apenas nutricionistas podem criar dietas.');
        }

        // Busca apenas os alunos vinculados a este nutri
        $students = $user->students()->get();

        return view('diets.create', compact('students'));
    }

    /**
     * Salva a dieta no banco.
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'nutri') {
            abort(403);
        }

        // Validar que student_id existe E pertence ao nutricionista autenticado
        $nutritionistId = $user->id;
        $studentId = $request->input('student_id');
        $studentBelongsToNutritionist = ProfessionalStudent::where('professional_id', $nutritionistId)
            ->where('student_id', $studentId)
            ->exists();
        
        if (!$studentBelongsToNutritionist) {
            abort(403, 'Este aluno não está vinculado a você.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string|max:255',
            'meals' => 'required|array|min:1',
            'meals.*.name' => 'required|string',
            'meals.*.time' => 'nullable',
            'meals.*.foods' => 'required|array|min:1',
            'meals.*.foods.*.name' => 'required|string',
            'meals.*.foods.*.quantity' => 'required|string',
            'meals.*.foods.*.calories' => 'nullable|string',
            'meals.*.foods.*.observation' => 'nullable|string',
        ]);

        // Criar o Plano
        $plan = DietPlan::create([
            'student_id' => $validated['student_id'],
            'nutritionist_id' => Auth::id(),
            'name' => $validated['name'],
            'goal' => $validated['goal'],
            'start_date' => now(),
            'is_active' => true,
        ]);

        // Criar Refeições e Alimentos
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
     * Exibe uma dieta específica.
     */
    public function show(DietPlan $diet)
    {
        // Verificar permissão
        if (Auth::id() !== $diet->student_id && Auth::id() !== $diet->nutritionist_id) {
            abort(403);
        }

        $diet->load('meals.foods');

        return view('diets.show', compact('diet'));
    }
}
