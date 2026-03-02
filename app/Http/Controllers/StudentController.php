<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BodyMeasurement;

class StudentController extends Controller
{
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Último treino ativo
        $activeWorkout = $user->workoutPlans()
            ->where('is_active', true)
            ->latest()
            ->first();
            
        if ($activeWorkout) {
            $activeWorkout->load('days.exercises');
        }

        // Última dieta
        $activeDiet = $user->dietPlans()->latest()->first();

        // Histórico de peso (últimos 5 registros para o gráfico simples)
        $weightHistory = $user->measurements()
            ->orderBy('date', 'asc')
            ->take(5)
            ->get();

        return view('student.dashboard', compact('activeWorkout', 'activeDiet', 'weightHistory'));
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
