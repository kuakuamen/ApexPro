<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Exercise;
use App\Models\ProfessionalStudent;
use App\Services\AiAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AiAssessmentController extends Controller
{
    protected $aiService;

    public function __construct(AiAnalysisService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Tela inicial da avaliação com IA.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Se não for personal, redirecionar
        if ($user->role !== 'personal') {
            abort(403, 'Apenas personal trainers podem acessar essa funcionalidade.');
        }
        
        // Busca apenas alunos do personal logado
        $students = $user->students()->orderBy('name')->get();
        
        return view('personal.ai-assessment.index', compact('students'));
    }

    /**
     * Processa as imagens e gera a análise + sugestão de treino.
     */
    public function analyze(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role !== 'personal') {
            abort(403, 'Apenas personal trainers podem fazer an\u00e1lises.');
        }

        // Validar que student_id existe E pertence ao personal autenticado
        $personalId = $user->id;
        $studentId = $request->input('student_id');
        $studentBelongsToPersonal = ProfessionalStudent::where('professional_id', $personalId)
            ->where('student_id', $studentId)
            ->exists();
        
        if (!$studentBelongsToPersonal) {
            abort(403, 'Este aluno n\u00e3o est\u00e1 vinculado a voc\u00ea.');
        }
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'goal' => 'required|string',
            'experience_level' => 'required|string',
            'additional_notes' => 'nullable|string',
            'photo_front' => 'required|image|max:5120', // 5MB
            'photo_side_right' => 'required|image|max:5120',
            'photo_side_left' => 'required|image|max:5120',
            'photo_back' => 'required|image|max:5120',
            'photo_extra' => 'nullable|array|max:6',
            'photo_extra.*' => 'nullable|image|max:5120',
        ]);

        $student = User::find($request->student_id);

        // Salvar imagens em disco PRIVADO (não público) para segurança
        $frontPath = $request->file('photo_front')->store('assessments', 'private');
        $sideRightPath = $request->file('photo_side_right')->store('assessments', 'private');
        $sideLeftPath = $request->file('photo_side_left')->store('assessments', 'private');
        $backPath = $request->file('photo_back')->store('assessments', 'private');
        $extraPaths = [];

        if ($request->hasFile('photo_extra')) {
            foreach ($request->file('photo_extra') as $extraPhoto) {
                $extraPaths[] = $extraPhoto->store('assessments', 'private');
            }
        }

        // BUSCA INTELIGENTE DE ANAMNESE (Última válida)
        // Procura a avaliação mais recente que tenha algum dado de anamnese preenchido
        $lastAnamnese = $student->measurements()
            ->where(function($q) {
                $q->whereNotNull('injuries')
                  ->orWhereNotNull('medications')
                  ->orWhereNotNull('surgeries');
            })
            ->latest('date')
            ->first();

        // Preparar dados para IA
        $studentData = [
            'age' => $student->birth_date ? $student->birth_date->age : 25, 
            'gender' => $student->gender ?? 'Masculino', 
            // 'profession' => $student->profession ?? 'Não informada', // Removido
            'goal' => $request->goal,
            'experience' => $request->experience_level,
            'anamnese' => [
                'injuries' => $lastAnamnese->injuries ?? 'Nenhuma registrada',
                'medications' => $lastAnamnese->medications ?? 'Nenhum registrado',
                'surgeries' => $lastAnamnese->surgeries ?? 'Nenhuma registrada',
                'pain_points' => $lastAnamnese->pain_points ?? 'Nenhuma dor relatada',
                'habits' => $lastAnamnese->habits ?? 'Não informado'
            ],
            'notes' => "Objetivo: {$request->goal}. Nível: {$request->experience_level}. " . 
                       // "Profissão: " . ($student->profession ?? 'Não inf.') . ". " . // Removido
                       "Observações do Personal: {$request->additional_notes}"
        ];

        // Chamar o serviço de IA
        $analysisResult = $this->aiService->analyzeImages(
            array_merge([$frontPath, $sideRightPath, $sideLeftPath, $backPath], $extraPaths), 
            $studentData
        );

        // Retornar a view de revisão com os dados preenchidos
        // Buscamos todos os exercícios para o select de edição
        $allExercises = Exercise::orderBy('name')->get();

        // SALVAR NA SESSÃO PARA O PDF E PARA O STORE FINAL
        session([
            'last_analysis_result' => $analysisResult,
            'last_front_path' => $frontPath,
            'last_side_path' => $sideRightPath,
            'last_side_left_path' => $sideLeftPath,
            'last_back_path' => $backPath,
            'last_extra_paths' => $extraPaths,
            // 'last_anamnese' => $studentData['anamnese'] // Removido
        ]);

        return view('personal.ai-assessment.review', compact(
            'student', 
            'analysisResult', 
            'allExercises',
            'frontPath', 'backPath',
            'request' // Passar dados do request original (goal, etc)
        ) + ['sidePath' => $sideRightPath]);
    }

    /**
     * Salva o treino aprovado pelo personal.
     */
    public function generatePdf(Request $request)
    {
        // Recupera os dados que foram enviados no form (igual ao store, mas renderiza view de impressão)
        $student = User::find($request->student_id);
        
        // Precisamos reconstruir o array analysisResult parcialmente ou passar os dados brutos
        // Como o PDF precisa dos dados de análise (postura, etc), e eles NÃO estão no form de aprovação (apenas visualmente),
        // o ideal seria ter persistido isso ou enviar via hidden input.
        // Para simplificar agora, vamos pegar o que der do request, mas a análise postural vai faltar se não passarmos.
        
        // SOLUÇÃO RÁPIDA: Vamos assumir que a análise postural foi salva na sessão ou passaremos como hidden fields no futuro.
        // Por enquanto, vou recriar um objeto simples para o PDF não quebrar, mas o ideal é persistir a análise.
        
        // Mas espere! A rota 'analyze' gerou o view 'review'. O 'review' tem os dados.
        // Quando damos submit no form 'review', enviamos apenas os inputs.
        // A melhor forma é salvar o resultado da análise em cache/sessão temporária.
        
        $analysisResult = session('last_analysis_result', []);
        
        // Se não tiver na sessão (ex: expirou), tenta pegar do request se implementarmos hidden fields, 
        // ou mostra aviso.
        
        // Vamos buscar os caminhos das imagens que devem estar no request ou sessão
        $frontPath = session('last_front_path');
        $sidePath = session('last_side_path');
        $backPath = session('last_back_path');
        $extraPaths = session('last_extra_paths', []);

        return view('personal.ai-assessment.pdf', compact(
            'student',
            'request',
            'analysisResult',
            'frontPath', 'sidePath', 'backPath'
        ));
    }

    /**
     * Refina a análise com base no feedback do personal.
     */
    public function refine(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'feedback' => 'required|string',
            'goal' => 'required|string',
        ]);

        $student = User::find($request->student_id);

        // Recuperar dados da sessão
        $previousAnalysis = session('last_analysis_result', []);
        $frontPath = session('last_front_path');
        $sidePath = session('last_side_path');
        $sideLeftPath = session('last_side_left_path');
        $backPath = session('last_back_path');
        $extraPaths = session('last_extra_paths', []);

        if (!$previousAnalysis || !$frontPath) {
            return back()->with('error', 'Sessão expirada. Por favor, refaça o upload das imagens.');
        }

        // Preparar dados para IA
        $studentData = [
            'age' => 25, 
            'gender' => 'Masculino',
            'goal' => $request->goal,
            'feedback' => $request->feedback
        ];

        // Chamar o serviço de IA para refinamento
        $analysisResult = $this->aiService->refineAnalysis(
            $previousAnalysis,
            $request->feedback,
            array_merge(array_filter([$frontPath, $sidePath, $sideLeftPath, $backPath]), is_array($extraPaths) ? $extraPaths : []),
            $studentData
        );

        // Atualizar sessão
        session(['last_analysis_result' => $analysisResult]);

        // Retornar a view de revisão com os novos dados
        $allExercises = Exercise::orderBy('name')->get();

        return view('personal.ai-assessment.review', compact(
            'student', 
            'analysisResult', 
            'allExercises',
            'frontPath', 'sidePath', 'backPath',
            'request'
        ));
    }

    /**
     * Salva o treino aprovado pelo personal.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role !== 'personal') {
            abort(403, 'Apenas personal trainers podem salvar treinos.');
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

        // Validação básica
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'workout_name' => 'required|string',
            'goal' => 'required|string',
            // Validação dos dias e exercícios seria mais complexa, vamos confiar no form por enquanto
        ]);

        DB::transaction(function () use ($request, $user) {
            $student = User::find($request->student_id);

            // 0. Salvar a Avaliação na Tabela Assessments
            $student->assessments()->create([
                'personal_id' => $user->id,
                'front_image_path' => session('last_front_path'), // Pega da sessão pois o form não reenvia arquivo
                'side_image_path' => session('last_side_path'),
                'back_image_path' => session('last_back_path'),
                'ai_analysis_data' => session('last_analysis_result'), // Salva o JSON da análise
                'status' => 'approved',
                // Removidos campos de anamnese pois não existem mais no form
                'goal' => $request->goal,
            ]);

            // 1. Criar o Plano de Treino
            $plan = $student->workoutPlans()->create([
                'name' => $request->workout_name,
                'goal' => $request->goal,
                'start_date' => now(),
                'end_date' => now()->addWeeks(8), // Padrão 8 semanas
                'is_active' => true,
                'personal_id' => $user->id,
            ]);

            // Desativar planos anteriores?
            // $student->workoutPlans()->where('id', '!=', $plan->id)->update(['is_active' => false]);

            // 2. Criar os Dias e Exercícios
            if ($request->has('days')) {
                foreach ($request->days as $dayData) {
                    if (empty($dayData['name'])) continue;

                    $workoutDay = $plan->days()->create([
                        'name' => $dayData['name'], // "Treino A", "Treino B"
                        'day_of_week' => null, // Opcional
                    ]);

                    if (isset($dayData['exercises'])) {
                        foreach ($dayData['exercises'] as $exIndex => $exData) {
                            if (empty($exData['name'])) continue;

                            // 2.2. Criar o exercício diretamente vinculado ao dia
                            $workoutDay->exercises()->create([
                                'name' => $exData['name'],
                                'sets' => $exData['sets'] ?? 3,
                                'reps' => $exData['reps'] ?? '10-12',
                                'observation' => $exData['notes'] ?? null,
                                'rest_time' => 60, // Default 60s
                                'order' => $exIndex
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('workouts.index')
            ->with('success', 'Treino gerado pela IA e aprovado com sucesso!');
    }

    /**
     * Gera treino SEM análise de imagens - apenas com dados do aluno
     */
    public function analyzeNoImages(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role !== 'personal') {
            abort(403, 'Apenas personal trainers podem acessar essa funcionalidade.');
        }
        // Se for GET, redireciona para o formulário
        if ($request->isMethod('get')) {
            return redirect()->route('personal.ai-assessment.index');
        }

        $request->validate([
            'student_id' => 'required|exists:users,id',
            'goal' => 'required|string',
            'experience_level' => 'required|string',
            'description' => 'nullable|string',
            'additional_notes' => 'nullable|string',
        ]);

        // Validar que student_id pertence ao personal autenticado
        $personalId = $user->id;
        $studentId = $request->input('student_id');
        $studentBelongsToPersonal = ProfessionalStudent::where('professional_id', $personalId)
            ->where('student_id', $studentId)
            ->exists();
        
        if (!$studentBelongsToPersonal) {
            abort(403, 'Este aluno n\u00e3o est\u00e1 vinculado a voc\u00ea.');
        }

        $student = User::find($request->student_id);

        // BUSCA INTELIGENTE DE ANAMNESE
        $lastAnamnese = $student->measurements()
            ->where(function($q) {
                $q->whereNotNull('injuries')
                  ->orWhereNotNull('medications')
                  ->orWhereNotNull('surgeries');
            })
            ->latest('date')
            ->first();

        // Preparar dados para IA (SEM IMAGENS)
        $studentData = [
            'age' => $student->birth_date ? $student->birth_date->age : 25, 
            'gender' => $student->gender ?? 'Masculino', 
            'goal' => $request->goal,
            'experience' => $request->experience_level,
            'description' => $request->description ?? 'Não informado',
            'anamnese' => [
                'injuries' => $lastAnamnese->injuries ?? 'Nenhuma registrada',
                'medications' => $lastAnamnese->medications ?? 'Nenhum registrado',
                'surgeries' => $lastAnamnese->surgeries ?? 'Nenhuma registrada',
                'pain_points' => $lastAnamnese->pain_points ?? 'Nenhuma dor relatada',
                'habits' => $lastAnamnese->habits ?? 'Não informado'
            ],
            'notes' => "Objetivo: {$request->goal}. Nível: {$request->experience_level}. " . 
                       "Observações: {$request->additional_notes}"
        ];

        // Chamar o serviço de IA (sem imagens)
        $analysisResult = $this->aiService->generateWorkoutWithoutImages($studentData);

        // Buscamos todos os exercícios para o select de edição
        $allExercises = Exercise::orderBy('name')->get();

        // Salvar na sessão
        session([
            'last_analysis_result' => $analysisResult,
            'last_front_path' => null,
            'last_side_path' => null,
            'last_side_left_path' => null,
            'last_back_path' => null,
            'last_extra_paths' => [],
        ]);

        return view('personal.ai-assessment.review', array_merge(
            compact('student', 'allExercises', 'analysisResult'),
            [
                'images' => [
                    'front' => null,
                    'side' => null,
                    'back' => null,
                ],
            ],
            ['request' => $request]
        ));
    }
}