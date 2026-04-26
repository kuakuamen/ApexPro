<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BodyMeasurement;
use App\Models\ProfessionalStudent;
use App\Models\WorkoutLog;
use App\Models\WorkoutPlan;
use App\Models\WorkoutDay;
use App\Models\DietPlan;
use Carbon\Carbon;

class StudentController extends Controller
{
    private function parseCaloriesValue(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        $normalized = str_replace(',', '.', trim((string) $value));
        if (!preg_match('/-?\d+(\.\d+)?/', $normalized, $matches)) {
            return 0.0;
        }

        $number = (float) $matches[0];
        return $number > 0 ? $number : 0.0;
    }

    private function calculateDietTotalCalories(DietPlan $diet): int
    {
        $total = 0.0;
        foreach ($diet->meals as $meal) {
            foreach ($meal->foods as $food) {
                $total += $this->parseCaloriesValue($food->calories);
            }
        }

        return (int) round($total);
    }

    private function resolveCurrentWorkoutDay(WorkoutPlan $workout, int $studentId): array
    {
        $workout->loadMissing('days.exercises');
        $daysOrdered = $workout->days->sortBy('order')->values();

        if ($daysOrdered->isEmpty()) {
            return [null, null];
        }

        $exerciseIds = $daysOrdered
            ->flatMap(fn($day) => $day->exercises->pluck('id'))
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $completedExerciseIds = $exerciseIds->isEmpty()
            ? []
            : WorkoutLog::where('student_id', $studentId)
                ->whereIn('exercise_id', $exerciseIds)
                ->pluck('exercise_id')
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

        $completedLookup = array_flip($completedExerciseIds);
        $currentDay = null;

        foreach ($daysOrdered as $day) {
            $totalExercises = $day->exercises->count();
            if ($totalExercises === 0) {
                continue;
            }

            $dayCompleted = $day->exercises->every(
                fn($exercise) => isset($completedLookup[(int) $exercise->id])
            );

            if (!$dayCompleted) {
                $currentDay = $day;
                break;
            }
        }

        if (!$currentDay) {
            $currentDay = $daysOrdered->last() ?: $daysOrdered->first();
        }

        $dayNumberIndex = $daysOrdered->search(
            fn($day) => (int) $day->id === (int) $currentDay->id
        );
        $currentDayNumber = $dayNumberIndex === false ? null : ((int) $dayNumberIndex + 1);

        return [$currentDay, $currentDayNumber];
    }

    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Buscar o profissional vinculado ao aluno
        $professionalStudent = ProfessionalStudent::where('student_id', $user->id)
            ->with('professional')
            ->first();

        $professional = $professionalStudent ? $professionalStudent->professional : null;

        // Último treino ativo
        $activeWorkout = $user->workoutPlans()
            ->where('is_active', true)
            ->latest()
            ->first();
            
        if ($activeWorkout) {
            $activeWorkout->load('days.exercises');
        }

        $currentWorkoutDay = null;
        $currentWorkoutDayNumber = null;
        if ($activeWorkout) {
            [$currentWorkoutDay, $currentWorkoutDayNumber] = $this->resolveCurrentWorkoutDay($activeWorkout, $user->id);
        }

        // Ultimo plano alimentar ativo
        $activeDiet = $user->dietPlans()
            ->where('is_active', true)
            ->with('meals.foods')
            ->latest()
            ->first();

        $activeDietTotalCalories = $activeDiet
            ? $this->calculateDietTotalCalories($activeDiet)
            : null;

        // Histórico de peso (últimos 5 registros)
        $weightHistory = $user->measurements()
            ->orderBy('date', 'asc')
            ->take(5)
            ->get();

        // Dias da semana com atividade (lun-dom da semana atual)
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $weekEnd   = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        $logsThisWeek = WorkoutLog::where('student_id', $user->id)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->dayOfWeekIso) // 1=seg ... 7=dom
            ->unique()
            ->values()
            ->toArray();

        // Total de dias treinados na semana
        $weekDaysWorked = count($logsThisWeek);
        $totalWorkoutDays = $activeWorkout ? $activeWorkout->days->count() : 5;

        // Streak — dias consecutivos até hoje com pelo menos 1 log
        $streak = 0;
        $checkDay = Carbon::today();
        while (true) {
            $hasLog = WorkoutLog::where('student_id', $user->id)
                ->whereDate('date', $checkDay->toDateString())
                ->exists();
            if (!$hasLog) break;
            $streak++;
            $checkDay->subDay();
        }

        // Último treino concluído (dia com logs, antes de hoje)
        $lastTrainingDate = WorkoutLog::where('student_id', $user->id)
            ->whereDate('date', '<', Carbon::today()->toDateString())
            ->orderByDesc('date')
            ->value('date');

        $lastTrainingDaysAgo = $lastTrainingDate
            ? Carbon::parse($lastTrainingDate)->diffInDays(Carbon::today())
            : null;

        return view('student.dashboard', compact(
            'user', 'professional', 'activeWorkout', 'activeDiet', 'activeDietTotalCalories', 'weightHistory',
            'logsThisWeek', 'weekDaysWorked', 'totalWorkoutDays',
            'streak', 'lastTrainingDate', 'lastTrainingDaysAgo',
            'currentWorkoutDay', 'currentWorkoutDayNumber'
        ));
    }

    public function activeWorkout(Request $request, WorkoutPlan $workout, WorkoutDay $day)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Garante que o treino pertence ao aluno
        abort_if($workout->student_id !== $user->id, 403);
        abort_if($day->workout_plan_id !== $workout->id, 404);

        $day->load('exercises');

        $todayDate = now('America/Sao_Paulo')->toDateString();
        $todayLogs = WorkoutLog::where('student_id', $user->id)
            ->whereDate('date', $todayDate)
            ->pluck('exercise_id')
            ->toArray();

        $requestedExerciseId = (int) $request->query('exercise_id', 0);
        $startExerciseId = null;

        if ($requestedExerciseId > 0) {
            $exerciseIds = $day->exercises->pluck('id')->map(fn($id) => (int) $id)->values();

            if ($exerciseIds->contains($requestedExerciseId)) {
                $startExerciseId = $requestedExerciseId;
            }
        }

        if (!$startExerciseId) {
            $startExerciseId = $day->exercises->first()?->id;
        }

        return view('student.active-workout', compact('workout', 'day', 'todayLogs', 'startExerciseId'));
    }

    public function progress()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Histórico de treinos por semana (últimas 8 semanas)
        $weeklyStats = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
            $weekEnd   = Carbon::now()->subWeeks($i)->endOfWeek(Carbon::SUNDAY);
            $count = WorkoutLog::where('student_id', $user->id)
                ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->distinct('date')->count('date');
            $weeklyStats[] = [
                'label' => $weekStart->format('d/M'),
                'count' => $count,
            ];
        }

        // Total geral de treinos
        $totalWorkouts = WorkoutLog::where('student_id', $user->id)
            ->distinct('date')->count('date');

        // Streak atual
        $streak = 0;
        $checkDay = Carbon::today();
        while (true) {
            $hasLog = WorkoutLog::where('student_id', $user->id)
                ->whereDate('date', $checkDay->toDateString())->exists();
            if (!$hasLog) break;
            $streak++;
            $checkDay->subDay();
        }

        // Melhor streak histórico
        $allDates = WorkoutLog::where('student_id', $user->id)
            ->distinct('date')->orderBy('date')->pluck('date')
            ->map(fn($d) => Carbon::parse($d));
        $bestStreak = 0; $cur = 0; $prev = null;
        foreach ($allDates as $d) {
            if ($prev && $d->diffInDays($prev) === 1) { $cur++; } else { $cur = 1; }
            $bestStreak = max($bestStreak, $cur);
            $prev = $d;
        }

        // Treinos este mês
        $thisMonthCount = WorkoutLog::where('student_id', $user->id)
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->distinct('date')->count('date');

        // Última medida corporal
        $latestMeasurement = $user->measurements()->orderByDesc('date')->first();

        return view('student.progress', compact(
            'user', 'weeklyStats', 'totalWorkouts', 'streak',
            'bestStreak', 'thisMonthCount', 'latestMeasurement'
        ));
    }

    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $professionalStudent = ProfessionalStudent::where('student_id', $user->id)
            ->with('professional')->first();
        $professional = $professionalStudent?->professional;
        $latestMeasurement = $user->measurements()->orderByDesc('date')->first();
        $totalWorkouts = WorkoutLog::where('student_id', $user->id)
            ->distinct('date')->count('date');

        return view('student.profile', compact('user', 'professional', 'latestMeasurement', 'totalWorkouts'));
    }

    public function updatePhoto(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate(['profile_photo' => ['required', 'image', 'max:5120']]);

        if (!empty($user->profile_photo_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->update([
            'profile_photo_path' => $request->file('profile_photo')->store('profile-photos', 'public'),
        ]);

        return back()->with('success', 'Foto atualizada com sucesso!');
    }

    /**
     * Tela de Evolução com Gráficos Completos.
     */
    public function evolution()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Busca todas as medidas ordenadas por data
        $measurements = $user->measurements()
            ->orderBy('date', 'asc')
            ->get();

        // Prepara dados para os gráficos
        $dates = $measurements->pluck('date')->map(fn($d) => $d->format('d/m/Y'));
        $weights = $measurements->pluck('weight');
        $bodyFats = $measurements->pluck('body_fat');
        $muscleMasses = $measurements->pluck('muscle_mass');
        
        // Medidas específicas
        $waists = $measurements->pluck('waist');
        $abdomens = $measurements->pluck('abdomen');

        return view('student.evolution', compact(
            'measurements', 
            'dates', 
            'weights', 
            'bodyFats', 
            'muscleMasses',
            'waists',
            'abdomens'
        ));
    }
}
