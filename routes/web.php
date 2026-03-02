<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PhotoController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WorkoutPlanController;
use App\Http\Controllers\DietPlanController;

use App\Http\Controllers\PersonalController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AiAssessmentController; // Importar o novo controller
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

// Rotas de Autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rota de Segurança: Servir Fotos com Validação de Permissão
Route::middleware('auth')->get('/photo/{assessmentId}/{type}', [PhotoController::class, 'show'])->name('photo.show');
Route::middleware('auth')->get('/measurement/{measurementId}/{type}', [PhotoController::class, 'showMeasurement'])->name('measurement.photo');

// Rotas do Personal (Área Administrativa)
Route::middleware(['auth', 'role:personal'])->prefix('personal')->name('personal.')->group(function () {
    Route::get('/dashboard', [PersonalController::class, 'dashboard'])->name('dashboard');
    
    // Alunos
    Route::get('/alunos/novo', [PersonalController::class, 'createStudent'])->name('students.create');
    Route::post('/alunos', [PersonalController::class, 'storeStudent'])->name('students.store');
    Route::get('/alunos/{student}', [PersonalController::class, 'showStudent'])->name('students.show');
    Route::patch('/alunos/{student}/toggle-status', [PersonalController::class, 'toggleStatus'])->name('students.toggle-status');
    
    // Medidas
    Route::get('/alunos/{student}/medidas/nova', [PersonalController::class, 'createMeasurement'])->name('measurements.create');
    Route::post('/alunos/{student}/medidas', [PersonalController::class, 'storeMeasurement'])->name('measurements.store');
    Route::get('/medidas/{measurement}/editar', [PersonalController::class, 'editMeasurement'])->name('measurements.edit');
    Route::put('/medidas/{measurement}', [PersonalController::class, 'updateMeasurement'])->name('measurements.update');
    Route::delete('/medidas/{measurement}', [PersonalController::class, 'destroyMeasurement'])->name('measurements.destroy');

    // NOVA ROTA: Avaliação com IA
    Route::get('/avaliacao-ia', [AiAssessmentController::class, 'index'])->name('ai-assessment.index');
    Route::post('/avaliacao-ia/analisar', [AiAssessmentController::class, 'analyze'])->name('ai-assessment.analyze');
    Route::match(['get', 'post'], '/avaliacao-ia/analisar-sem-imagens', [AiAssessmentController::class, 'analyzeNoImages'])->name('ai-assessment.analyze-no-images');
    Route::post('/avaliacao-ia/salvar', [AiAssessmentController::class, 'store'])->name('ai-assessment.store');
    Route::post('/avaliacao-ia/pdf', [AiAssessmentController::class, 'generatePdf'])->name('ai-assessment.pdf');
    Route::post('/avaliacao-ia/refinar', [AiAssessmentController::class, 'refine'])->name('ai-assessment.refine');
    
    // Rota de Teste Gemini (CURL DIRETO)
    Route::get('/test-gemini', function() {
        $apiKey = env('GEMINI_API_KEY');
        $url = "https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        dd($data);
    });
});

// Rotas do Administrador
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Gerenciamento de Personals
    Route::get('/personals', [AdminController::class, 'listPersonals'])->name('personals.index');
    Route::get('/personals/novo', [AdminController::class, 'createPersonal'])->name('personals.create');
    Route::post('/personals', [AdminController::class, 'storePersonal'])->name('personals.store');
    Route::get('/personals/{user}', [AdminController::class, 'showPersonal'])->name('personals.show');
    Route::get('/personals/{user}/editar', [AdminController::class, 'editPersonal'])->name('personals.edit');
    Route::put('/personals/{user}', [AdminController::class, 'updatePersonal'])->name('personals.update');
    Route::patch('/personals/{user}/status', [AdminController::class, 'togglePersonalStatus'])->name('personals.toggle');
    Route::patch('/personals/{user}/licenca', [AdminController::class, 'updateLicenseStatus'])->name('personals.license');
    Route::delete('/personals/{user}', [AdminController::class, 'deletePersonal'])->name('personals.delete');
    Route::get('/personals/{user}/alunos', [AdminController::class, 'personalStudents'])->name('personals.students');
    
    // Gerenciamento Geral de Usuários
    Route::get('/usuarios', [AdminController::class, 'allUsers'])->name('users.index');
    
    // Logs
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
});

// Rotas de Funcionalidades (Acessíveis a usuários logados)
Route::middleware(['auth'])->group(function () {
    // Rotas de Treino
    Route::get('/treinos', [WorkoutPlanController::class, 'index'])->name('workouts.index');
    Route::get('/treinos/novo', [WorkoutPlanController::class, 'create'])->name('workouts.create');
    Route::post('/treinos', [WorkoutPlanController::class, 'store'])->name('workouts.store');
    Route::get('/treinos/{workout}/editar', [WorkoutPlanController::class, 'edit'])->name('workouts.edit');
    Route::put('/treinos/{workout}', [WorkoutPlanController::class, 'update'])->name('workouts.update');
    Route::get('/treinos/{workout}', [WorkoutPlanController::class, 'show'])->name('workouts.show');

    // Rotas de Dieta (Nutrição)
    Route::get('/dietas', [DietPlanController::class, 'index'])->name('diets.index');
    Route::get('/dietas/nova', [DietPlanController::class, 'create'])->name('diets.create');
    Route::post('/dietas', [DietPlanController::class, 'store'])->name('diets.store');
    Route::get('/dietas/{diet}', [DietPlanController::class, 'show'])->name('diets.show');
    
    // Rota de Toggle de Exercício (Aluno)
    Route::post('/aluno/exercicio/{exerciseId}/toggle', [WorkoutPlanController::class, 'toggleExercise'])->where('exerciseId', '[0-9]+')->name('student.exercise.toggle');
    
    // Debug: Verificar se rota está acessível
    Route::get('/debug/toggle-route', function () {
        return response()->json([
            'message' => 'Rota de toggle está acessível',
            'user_id' => Auth::user()?->id,
            'user_role' => Auth::user()?->role,
            'csrf_token' => csrf_token()
        ]);
    });
    
    // Rota de teste
    Route::get('/test-route', function () {
        return response()->json(['message' => 'Rota de teste funcionando!', 'user' => Auth::user()?->id]);
    });
    
    // Rotas do Aluno
    Route::middleware('role:aluno')->group(function () {
        Route::get('/aluno/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/aluno/evolucao', [StudentController::class, 'evolution'])->name('student.evolution');
    });
});

// Rota raiz inteligente
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    $user = Auth::user();
    
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'personal') {
        return redirect()->route('personal.dashboard');
    } elseif ($user->role === 'nutri') {
        return redirect()->route('diets.index');
    } elseif ($user->role === 'aluno') {
        return redirect()->route('student.dashboard');
    } else {
        return redirect()->route('workouts.index'); // Fallback seguro
    }
});

// Rota de Fallback para Imagens no Windows/XAMPP
Route::get('/storage/{path}', function ($path) {
    $path = storage_path('app/public/' . $path);

    if (!file_exists($path)) {
        abort(404);
    }

    $file = \Illuminate\Support\Facades\File::get($path);
    $type = \Illuminate\Support\Facades\File::mimeType($path);

    $response = \Illuminate\Support\Facades\Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
})->where('path', '.*');
