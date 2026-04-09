<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\ProfessionalStudent;
use App\Services\AiAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssessmentController extends Controller
{
    protected $aiService;

    public function __construct(AiAnalysisService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Exibe o formulário de envio de fotos para análise.
     */
    public function create()
    {
        // TODO: Retornar a view com o formulário
        return view('assessments.create');
    }

    /**
     * Processa o envio, salva imagens e chama a IA.
     */
    public function store(Request $request)
    {
        $request->validate([
            'front_image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'side_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'back_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Salvar Imagens em disco PRIVADO (seguro)
        $frontPath = \App\Helpers\ImageHelper::compressAndStore($request->file('front_image'), 'assessments', 'private');
        $sidePath  = \App\Helpers\ImageHelper::compressAndStore($request->file('side_image'), 'assessments', 'private');
        $backPath  = \App\Helpers\ImageHelper::compressAndStore($request->file('back_image'), 'assessments', 'private');

        // Chamar Serviço de IA (Simulado por enquanto)
        $aiResult = $this->aiService->analyzeImages(
            [$frontPath, $sidePath, $backPath],
            ['user_id' => $user->id, 'notes' => $request->notes]
        );

        // Criar Avaliação no Banco
        $assessment = Assessment::create([
            'student_id' => $user->id,
            'personal_id' => null, // Personal assumirá depois
            'front_image_path' => $frontPath,
            'side_image_path' => $sidePath,
            'back_image_path' => $backPath,
            'ai_analysis_data' => $aiResult,
            'status' => 'pending_review',
        ]);

        return redirect()->route('assessments.show', $assessment)
            ->with('success', 'Fotos enviadas! A IA já processou sua análise preliminar.');
    }

    /**
     * Exibe o resultado da análise (para o aluno e para o personal aprovar).
     */
    public function show(Assessment $assessment)
    {
        // Garantir que o usuário pode ver essa avaliação
        if (Auth::id() === $assessment->student_id) {
            // O próprio aluno pode ver
            return view('assessments.show', compact('assessment'));
        }
        
        if (Auth::user()->role === 'personal') {
            // Personal pode ver APENAS suas próprias avaliações de seus alunos
            $isOwnStudent = ProfessionalStudent::where('professional_id', Auth::id())
                ->where('student_id', $assessment->student_id)
                ->exists();
            
            if (!$isOwnStudent) {
                abort(403, 'Você não tem permissão para acessar esta avaliação.');
            }
            
            return view('assessments.show', compact('assessment'));
        }
        
        abort(403);
    }
}
