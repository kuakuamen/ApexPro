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
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Auth;


// Webhook Mercado Pago (sem CSRF, sem auth)
Route::post('/webhooks/mercadopago', [WebhookController::class, 'handle'])->name('webhook.mercadopago'); // recebe payment e preapproval

// Rotas de Assinatura (PÃƒÂºblicas)
Route::get('/planos', [SubscriptionController::class, 'index'])->name('plans.index');
Route::get('/planos/{plan}/checkout', [SubscriptionController::class, 'checkout'])->name('plans.checkout');
Route::post('/planos/{plan}/process', [SubscriptionController::class, 'processPayment'])->name('plans.process');
Route::post('/planos/{plan}/card-test', [SubscriptionController::class, 'processCardTest'])->name('plans.card-test');
Route::get('/assinatura/cadastro', [SubscriptionController::class, 'showRegisterForm'])->name('subscription.register');
Route::post('/assinatura/cadastro', [SubscriptionController::class, 'storePersonal'])->name('subscription.store');
Route::get('/assinatura/status/{ref}', [SubscriptionController::class, 'checkStatus'])->name('subscription.status');
Route::get('/assinatura/pix/{ref}', [SubscriptionController::class, 'pixWaiting'])->name('subscription.pix-waiting');
Route::get('/assinatura/resultado/{ref}', [SubscriptionController::class, 'paymentResult'])->name('subscription.payment-result');

// Rotas de RenovaÃƒÂ§ÃƒÂ£o (Autenticadas, mas acessÃƒÂ­veis mesmo expirado)
Route::middleware('auth')->group(function () {
    Route::get('/assinatura/renovar', [SubscriptionController::class, 'showRenew'])->name('subscription.renew');
    Route::get('/assinatura/renovar/{plan}/checkout', [SubscriptionController::class, 'renewCheckout'])->name('subscription.renew.checkout');
    Route::post('/assinatura/renovar/{plan}', [SubscriptionController::class, 'processRenew'])->name('subscription.renew.process');
    Route::get('/assinatura/historico', [SubscriptionController::class, 'history'])->name('subscription.history');
    Route::post('/assinatura/cancelar', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
});

// Rotas de AutenticaÃƒÂ§ÃƒÂ£o
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/esqueci-a-senha', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/esqueci-a-senha', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/redefinir-senha/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/redefinir-senha', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/register', function () {
        return redirect()->route('plans.index');
    })->name('register');
});

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rota de SeguranÃƒÂ§a: Servir Fotos com ValidaÃƒÂ§ÃƒÂ£o de PermissÃƒÂ£o
Route::middleware('auth')->get('/photo/{assessmentId}/{type}', [PhotoController::class, 'show'])->name('photo.show');
Route::middleware('auth')->get('/measurement/{measurementId}/{type}', [PhotoController::class, 'showMeasurement'])->name('measurement.photo');
Route::middleware('auth')->get('/measurement/{measurementId}/extra/{index}', [PhotoController::class, 'showMeasurementExtra'])->whereNumber('index')->name('measurement.photo.extra');

// Rotas do Personal (ÃƒÂrea Administrativa)
Route::middleware(['auth', 'role:personal', 'subscription'])->prefix('personal')->name('personal.')->group(function () {
    Route::get('/dashboard', [PersonalController::class, 'dashboard'])->name('dashboard');
    Route::get('/perfil',    [PersonalController::class, 'showProfile'])->name('profile');
    Route::put('/perfil',    [PersonalController::class, 'updateProfile'])->name('profile.update');
    
    // Alunos
    Route::get('/alunos', [PersonalController::class, 'studentsIndex'])->name('students.index');
    Route::get('/alunos/novo', [PersonalController::class, 'createStudent'])->name('students.create');
    Route::post('/alunos', [PersonalController::class, 'storeStudent'])->name('students.store');
Route::get('/alunos/{student}', [PersonalController::class, 'showStudent'])->name('students.show');
Route::get('/alunos/{student}/editar', [PersonalController::class, 'editStudent'])->name('students.edit'); // Nova rota de ediÃƒÂ§ÃƒÂ£o
Route::put('/alunos/{student}', [PersonalController::class, 'updateStudent'])->name('students.update'); // Nova rota de atualizaÃƒÂ§ÃƒÂ£o
Route::patch('/alunos/{student}/foto', [PersonalController::class, 'updateStudentPhoto'])->name('students.photo');
Route::patch('/alunos/{student}/toggle-status', [PersonalController::class, 'toggleStatus'])->name('students.toggle-status');
Route::patch('/alunos/{student}/reset-password', [PersonalController::class, 'resetStudentPassword'])->name('students.reset-password');
    
    // Medidas
    Route::get('/alunos/{student}/medidas/nova', [PersonalController::class, 'createMeasurement'])->name('measurements.create');
    Route::post('/alunos/{student}/medidas', [PersonalController::class, 'storeMeasurement'])->name('measurements.store');
    Route::get('/medidas/{measurement}/editar', [PersonalController::class, 'editMeasurement'])->name('measurements.edit');
    Route::put('/medidas/{measurement}', [PersonalController::class, 'updateMeasurement'])->name('measurements.update');
    Route::delete('/medidas/{measurement}', [PersonalController::class, 'destroyMeasurement'])->name('measurements.destroy');

    // NOVA ROTA: AvaliaÃƒÂ§ÃƒÂ£o com IA
    Route::get('/avaliacao-ia', [AiAssessmentController::class, 'index'])->name('ai-assessment.index');
    Route::get('/avaliacao-ia/ultimas-imagens/{student}', [AiAssessmentController::class, 'getLastImages'])->name('ai-assessment.last-images');
    Route::post('/avaliacao-ia/analisar', [AiAssessmentController::class, 'analyze'])->name('ai-assessment.analyze');
    Route::match(['get', 'post'], '/avaliacao-ia/analisar-sem-imagens', [AiAssessmentController::class, 'analyzeNoImages'])->name('ai-assessment.analyze-no-images');
    Route::post('/avaliacao-ia/salvar', [AiAssessmentController::class, 'store'])->name('ai-assessment.store');
    Route::post('/avaliacao-ia/pdf', [AiAssessmentController::class, 'generatePdf'])->name('ai-assessment.pdf');
    Route::post('/avaliacao-ia/refinar', [AiAssessmentController::class, 'refine'])->name('ai-assessment.refine');
    Route::get('/avaliacao-ia/historico/{assessment}/pdf', [AiAssessmentController::class, 'exportSavedPdf'])->name('ai-assessment.saved-pdf');
    
    // EvoluÃƒÂ§ÃƒÂ£o de Alunos
    Route::get('/evolucao', [PersonalController::class, 'evolutionIndex'])->name('evolution.index');
    Route::get('/evolucao/dados/{student}', [PersonalController::class, 'evolutionData'])->name('evolution.data');

    // NOVA ROTA: AvaliaÃƒÂ§ÃƒÂµes Pendentes (Atrasadas / Sem AvaliaÃƒÂ§ÃƒÂ£o)
    Route::get('/avaliacoes/pendentes', [PersonalController::class, 'pendingAssessments'])->name('assessments.pending');

    // MÃƒÂ³dulo Financeiro
    Route::prefix('financeiro')->name('financial.')->group(function () {
        Route::get('/',                              [FinancialController::class, 'dashboard'])             ->name('dashboard');
        Route::get('/planos',                        [FinancialController::class, 'plans'])                 ->name('plans');
        Route::get('/planos/novo',                   [FinancialController::class, 'createPlan'])            ->name('plans.create');
        Route::post('/planos',                       [FinancialController::class, 'storePlan'])             ->name('plans.store');
        Route::get('/planos/{plan}/editar',          [FinancialController::class, 'editPlan'])              ->name('plans.edit');
        Route::put('/planos/{plan}',                 [FinancialController::class, 'updatePlan'])            ->name('plans.update');
        Route::delete('/planos/{plan}',              [FinancialController::class, 'destroyPlan'])           ->name('plans.destroy');
        Route::get('/vinculos',                      [FinancialController::class, 'studentPlans'])          ->name('student-plans');
        Route::get('/vinculos/novo',                 [FinancialController::class, 'assignPlan'])            ->name('student-plans.create');
        Route::post('/vinculos',                     [FinancialController::class, 'storePlanAssignment'])   ->name('student-plans.store');
        Route::get('/vinculos/{sp}/editar',          [FinancialController::class, 'editAssignment'])        ->name('student-plans.edit');
        Route::put('/vinculos/{sp}',                 [FinancialController::class, 'updateAssignment'])      ->name('student-plans.update');
        Route::patch('/vinculos/{sp}/toggle-acesso', [FinancialController::class, 'toggleStudentAccess'])   ->name('student-plans.toggle');
        Route::delete('/vinculos/{sp}',              [FinancialController::class, 'destroyAssignment'])     ->name('student-plans.destroy');
        Route::post('/verificar-inadimplencia',      [FinancialController::class, 'runSuspendCheck'])        ->name('run-suspend-check');
        Route::get('/pagamentos',                    [FinancialController::class, 'payments'])              ->name('payments');
        Route::post('/pagamentos',                   [FinancialController::class, 'storePayment'])          ->name('payments.store');
        Route::patch('/pagamentos/{p}/pago',         [FinancialController::class, 'markPaid'])              ->name('payments.mark-paid');
        Route::patch('/pagamentos/{p}/estornar',     [FinancialController::class, 'reversePayment'])        ->name('payments.reverse');
        Route::post('/gerar-cobrancas',              [FinancialController::class, 'generateMonthlyPayments'])->name('generate');
        Route::get('/relatorios',                    [FinancialController::class, 'reports'])               ->name('reports');
        Route::get('/relatorios/exportar-pdf',       [FinancialController::class, 'exportPdf'])            ->name('reports.export-pdf');
        Route::get('/relatorios/exportar-excel',     [FinancialController::class, 'exportExcel'])          ->name('reports.export-excel');
    });

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
    
    // Gestão de Planos
    Route::get('/planos', [AdminController::class, 'plansIndex'])->name('plans.index');
    Route::get('/planos/{planId}/editar', [AdminController::class, 'plansEdit'])->name('plans.edit');
    Route::put('/planos/{planId}', [AdminController::class, 'plansUpdate'])->name('plans.update');
    Route::patch('/planos/{planId}/desconto', [AdminController::class, 'plansDiscount'])->name('plans.discount');
    Route::patch('/planos/{planId}/remover-desconto', [AdminController::class, 'plansRemoveDiscount'])->name('plans.remove-discount');
    Route::patch('/planos/{planId}/toggle', [AdminController::class, 'plansToggle'])->name('plans.toggle');

    // Gerenciamento Geral de UsuÃƒÂ¡rios
    Route::get('/usuarios', [AdminController::class, 'allUsers'])->name('users.index');
    Route::get('/usuarios/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::patch('/usuarios/{user}/reset-senha', [AdminController::class, 'resetUserPassword'])->name('users.reset-password');
    Route::patch('/usuarios/{user}/assinatura/ativar', [AdminController::class, 'activateSubscription'])->name('users.subscription.activate');
    Route::patch('/usuarios/{user}/assinatura/suspender', [AdminController::class, 'suspendSubscription'])->name('users.subscription.suspend');
    Route::patch('/usuarios/{user}/assinatura/estender', [AdminController::class, 'extendSubscription'])->name('users.subscription.extend');

    // Logs
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
});

// Rotas de Funcionalidades (AcessÃƒÂ­veis a usuÃƒÂ¡rios logados)
Route::middleware(['auth', 'subscription'])->group(function () {
    // Rotas de Treino
    Route::get('/treinos', [WorkoutPlanController::class, 'index'])->name('workouts.index');
    Route::get('/treinos/novo', [WorkoutPlanController::class, 'create'])->name('workouts.create');
    Route::post('/treinos', [WorkoutPlanController::class, 'store'])->name('workouts.store');
    Route::get('/treinos/{workout}/editar', [WorkoutPlanController::class, 'edit'])->name('workouts.edit');
    Route::put('/treinos/{workout}', [WorkoutPlanController::class, 'update'])->name('workouts.update');
    Route::delete('/treinos/{workout}', [WorkoutPlanController::class, 'destroy'])->name('workouts.destroy');
    Route::patch('/treinos/{workout}/toggle-active', [WorkoutPlanController::class, 'toggleActive'])->name('workouts.toggle-active');
    Route::get('/treinos/{workout}', [WorkoutPlanController::class, 'show'])->name('workouts.show');

    // Rotas de Dieta (NutriÃƒÂ§ÃƒÂ£o)
    Route::get('/dietas', [DietPlanController::class, 'index'])->name('diets.index');
    Route::get('/dietas/nova', [DietPlanController::class, 'create'])->name('diets.create');
    Route::post('/dietas', [DietPlanController::class, 'store'])->name('diets.store');
    Route::get('/dietas/{diet}', [DietPlanController::class, 'show'])->name('diets.show');
    
    // Rota de Toggle de ExercÃƒÂ­cio (Aluno)
    Route::post('/aluno/exercicio/{exerciseId}/toggle', [WorkoutPlanController::class, 'toggleExercise'])->where('exerciseId', '[0-9]+')->name('student.exercise.toggle');
    Route::get('/aluno/exercicio/youtube', [WorkoutPlanController::class, 'exerciseYoutubeVideo'])->name('student.exercise.youtube');
    
    // Debug: Verificar se rota estÃƒÂ¡ acessÃƒÂ­vel
    Route::get('/debug/toggle-route', function () {
        return response()->json([
            'message' => 'Rota de toggle estÃƒÂ¡ acessÃƒÂ­vel',
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
Route::match(['get', 'head'], '/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    $user = Auth::user();
    
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'personal') {
        // Verificar se a assinatura expirou
        if ($user->subscription_expires_at && $user->subscription_expires_at->isPast()) {
            return redirect()->route('subscription.renew');
        }
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
