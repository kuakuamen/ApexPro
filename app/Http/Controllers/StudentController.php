<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BodyMeasurement;
use App\Models\ProfessionalStudent;
use App\Models\WorkoutLog;
use Carbon\Carbon;

class StudentController extends Controller
{
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
            'user', 'professional', 'activeWorkout', 'weightHistory',
            'logsThisWeek', 'weekDaysWorked', 'totalWorkoutDays',
            'streak', 'lastTrainingDate', 'lastTrainingDaysAgo'
        ));
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
