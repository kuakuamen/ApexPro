<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfessionalStudent;
use App\Models\BodyMeasurement;
use App\Services\BodyCompositionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Rules\Cpf;

class PersonalController extends Controller
{
    /**
     * Verificar se o aluno pertence a este personal.
     */
    protected function validateStudentBelongsToPersonal(User $student)
    {
        $isOwnStudent = ProfessionalStudent::where('student_id', $student->id)
            ->where('professional_id', Auth::id())
            ->exists();

        if (!$isOwnStudent) {
            abort(403, 'Você não tem permissão para acessar este aluno.');
        }
    }

    /**
     * Dashboard do Personal: Lista de Alunos.
     */
    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();

        // Carrega APENAS os alunos vinculados a este personal
        $students = $user->students()
            ->with(['measurements' => function ($query) {
                $query->latest('date');
            }])
            ->orderBy('name')
            ->get();

        $totalStudents = $students->count();
        $activeStudents = $students->where('is_active', true)->count();
        $newThisMonth = $students->filter(
            fn ($student) => $student->created_at && $student->created_at->greaterThanOrEqualTo(now()->startOfMonth())
        )->count();

        $studentsWithOldAssessments = $students->filter(function ($student) {
            if ($student->measurements->isEmpty()) {
                return false;
            }

            $lastAssessment = $student->measurements->first();
            $frequency = $student->assessment_frequency ?? 30;
            
            return $lastAssessment->date && $lastAssessment->date->diffInDays(now()) > $frequency;
        })->values();

        $pendingAssessmentsCount = $studentsWithOldAssessments->count();

        $pendingAssessmentsList = $studentsWithOldAssessments
            ->map(function ($student) {
                $lastAssessment = $student->measurements->first();
                $frequency = $student->assessment_frequency ?? 30;
                $daysSince = $lastAssessment?->date ? $lastAssessment->date->diffInDays(now()) : 0;
                $overdue = max(0, $daysSince - $frequency);

                return [
                    'student' => $student,
                    'last_assessment_date' => $lastAssessment?->date,
                    'days_without_assessment' => $daysSince,
                    'days_overdue' => $overdue,
                ];
            })
            ->sortByDesc('days_overdue')
            ->take(5)
            ->values();

        $studentsWithoutAssessmentCount = $students->filter(
            fn ($student) => $student->measurements->isEmpty()
        )->count();

        $studentsWithoutFirstAssessmentList = $students
            ->filter(fn ($student) => $student->measurements->isEmpty())
            ->sortBy('name')
            ->take(5)
            ->values();

        return view('personal.dashboard', compact(
            'students',
            'totalStudents',
            'activeStudents',
            'newThisMonth',
            'pendingAssessmentsCount',
            'pendingAssessmentsList',
            'studentsWithoutAssessmentCount',
            'studentsWithoutFirstAssessmentList'
        ));
    }

    /**
     * Listagem de alunos do personal.
     */
    public function studentsIndex()
    {
        /** @var User $user */
        $user = Auth::user();

        $students = $user->students()
            ->with(['measurements' => function($query) {
                $query->latest('date');
            }])
            ->orderBy('name')
            ->get();

        return view('personal.students.index', compact('students'));
    }

    /**
     * Listagem de alunos com pendências de avaliação (atrasada ou inexistente).
     */
    public function pendingAssessments(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $type = $request->query('type', 'all'); // 'overdue', 'missing', or 'all'
        $search = $request->query('search');

        $query = $user->students()
            ->with(['measurements' => function($q) {
                $q->latest('date');
            }])
            ->where('is_active', true);

        // Filtro por nome (busca)
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $allStudents = $query->orderBy('name')->get();

        // Filtrar na coleção (PHP) pois a lógica de data é complexa para SQL puro sem subqueries pesadas
        $students = $allStudents->filter(function ($student) use ($type) {
            $lastAssessment = $student->measurements->first();
            
            // Caso 1: Sem avaliação
            if ($student->measurements->isEmpty()) {
                return $type === 'missing' || $type === 'all';
            }

            // Caso 2: Avaliação atrasada (> frequência do aluno)
            $frequency = $student->assessment_frequency ?? 30;
            
            if ($lastAssessment && $lastAssessment->date && $lastAssessment->date->diffInDays(now()) > $frequency) {
                return $type === 'overdue' || $type === 'all';
            }

            return false;
        });

        // Adicionar metadados para a view
        $students->transform(function ($student) {
            $lastAssessment = $student->measurements->first();
            $student->last_assessment_date = $lastAssessment?->date;
            
            $daysSince = $lastAssessment?->date ? $lastAssessment->date->diffInDays(now()) : 0;
            $frequency = $student->assessment_frequency ?? 30;
            $overdue = max(0, $daysSince - $frequency);

            $student->days_without_assessment = $daysSince;
            $student->days_overdue = $overdue;

            if ($student->measurements->isEmpty()) {
                $student->status_label = 'Sem Avaliação';
                $student->status_color = 'red';
            } else {
                $student->status_label = 'Atrasada (' . intval($overdue) . ' dias)';
                $student->status_color = 'orange';
            }
            
            return $student;
        });

        // Ordenação: Prioridade para quem nunca avaliou, depois os mais atrasados
        $students = $students->sortByDesc(function ($student) {
            return $student->measurements->isEmpty() ? 999999 : $student->days_overdue;
        })->values(); // Resetar chaves para paginação manual se necessário, ou apenas limpar

        return view('personal.assessments.pending', compact('students', 'type', 'search'));
    }

    /**
     * Formulário para cadastrar novo aluno.
     */
    public function createStudent()
    {
        return view('personal.students.create');
    }

    /**
     * Salva novo aluno e cria vínculo.
     */
    public function storeStudent(Request $request)
    {
        // Limpa CPF e Telefone antes da validação para garantir unicidade correta
        $request->merge([
            'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'cpf' => ['required', 'string', 'unique:users,cpf', new Cpf],
            'birth_date' => 'required|date',
            'gender' => 'required|string',
            'assessment_frequency' => 'required|integer|in:15,30,60,90',
            'password' => 'required|min:6',
            'address_cep' => ['required', 'regex:/^\d{5}-\d{3}$/'],
            'address_state' => 'required|string|size:2',
            'address_city' => 'required|string|max:255',
            'address_street' => 'required|string|max:255',
            'address_neighborhood' => 'required|string|max:255',
            'address_number' => 'required|string|max:30',
        ]);

        $validated['address_state'] = mb_strtoupper($validated['address_state']);
        $validated['address'] = "{$validated['address_street']}, {$validated['address_number']} - {$validated['address_neighborhood']}, {$validated['address_city']}/{$validated['address_state']} - CEP {$validated['address_cep']}";

        DB::transaction(function () use ($validated) {
            // 1. Criar Usuário
            $student = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'cpf' => $validated['cpf'],
                'address' => $validated['address'],
                'address_cep' => $validated['address_cep'],
                'address_state' => $validated['address_state'],
                'address_city' => $validated['address_city'],
                'address_street' => $validated['address_street'],
                'address_neighborhood' => $validated['address_neighborhood'],
                'address_number' => $validated['address_number'],
                'birth_date' => $validated['birth_date'],
                'gender' => $validated['gender'],
                'assessment_frequency' => $validated['assessment_frequency'],
                'password' => Hash::make($validated['password']),
                'role' => 'aluno',
            ]);

            // 2. Criar Vínculo
            ProfessionalStudent::create([
                'student_id' => $student->id,
                'professional_id' => Auth::id(),
                'type' => 'personal',
            ]);
        });

        return redirect()->route('personal.dashboard')->with('success', 'Aluno cadastrado com sucesso!');
    }

    /**
     * Tela de edição de perfil do aluno.
     */
    public function editStudent(User $student)
    {
        $this->validateStudentBelongsToPersonal($student);
        return view('personal.students.edit', compact('student'));
    }

    /**
     * Atualiza dados do aluno.
     */
    public function updateStudent(Request $request, User $student)
    {
        $this->validateStudentBelongsToPersonal($student);

        // Limpa CPF e Telefone antes da validação
        $request->merge([
            'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'cpf' => ['required', 'string', new Cpf, 'unique:users,cpf,' . $student->id],
            'birth_date' => 'required|date',
            'gender' => 'required|string',
            'assessment_frequency' => 'required|integer|in:15,30,60,90',
            'address_cep' => ['required', 'regex:/^\d{5}-\d{3}$/'],
            'address_state' => 'required|string|size:2',
            'address_city' => 'required|string|max:255',
            'address_street' => 'required|string|max:255',
            'address_neighborhood' => 'required|string|max:255',
            'address_number' => 'required|string|max:30',
        ]);

        $validated['address_state'] = mb_strtoupper($validated['address_state']);
        $validated['address'] = "{$validated['address_street']}, {$validated['address_number']} - {$validated['address_neighborhood']}, {$validated['address_city']}/{$validated['address_state']} - CEP {$validated['address_cep']}";

        $student->update($validated);

        return redirect()->route('personal.students.show', $student)
            ->with('success', 'Perfil do aluno atualizado com sucesso!');
    }

    /**
     * Perfil do Aluno (Visão do Personal).
     */
    public function showStudent(User $student)
    {
        // Verificar se o aluno pertence a este personal
        $this->validateStudentBelongsToPersonal($student);

        // Carregar dados relacionados
        $measurements = BodyMeasurement::where('student_id', $student->id)->latest()->get();
        $workouts = $student->workoutPlans()->latest()->get();
        $diets = $student->dietPlans()->latest()->get();

        // Dados para os gráficos de evolução (ordem cronológica)
        $measurementsChronological = BodyMeasurement::where('student_id', $student->id)
            ->orderBy('date')
            ->get();
        $evolutionDates       = $measurementsChronological->pluck('date')->map(fn($d) => $d ? $d->format('d/m/Y') : null)->filter()->values()->toArray();
        $evolutionWeights     = $measurementsChronological->pluck('weight')->toArray();
        $evolutionMuscleMasses = $measurementsChronological->pluck('muscle_mass')->toArray();
        $evolutionBodyFats    = $measurementsChronological->pluck('body_fat')->toArray();

        return view('personal.students.show', compact(
            'student', 'measurements', 'workouts', 'diets',
            'measurementsChronological', 'evolutionDates', 'evolutionWeights', 'evolutionMuscleMasses', 'evolutionBodyFats'
        ));
    }

    /**
     * Tela para adicionar nova medida.
     */
    public function createMeasurement(User $student)
    {
        $this->validateStudentBelongsToPersonal($student);
        $previousMeasurement = BodyMeasurement::where('student_id', $student->id)->latest()->first();
        return view('personal.measurements.create', compact('student', 'previousMeasurement'));
    }

    /**
     * Salva nova medida.
     */
    public function storeMeasurement(Request $request, User $student)
    {
        $this->validateStudentBelongsToPersonal($student);
        
        // Pré-processamento: Trocar vírgula por ponto em todos os campos numéricos
        $input = $request->all();
        foreach ($input as $key => $value) {
            if (is_string($value) && is_numeric(str_replace(',', '.', $value))) {
                $input[$key] = str_replace(',', '.', $value);
            }
        }
        $request->replace($input);

        $validated = $request->validate([
            'date' => 'required|date',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'body_fat' => 'nullable|numeric',
            'muscle_mass' => 'nullable|numeric',
            'chest' => 'nullable|numeric',
            'left_arm' => 'nullable|numeric',
            'right_arm' => 'nullable|numeric',
            'waist' => 'nullable|numeric',
            'abdomen' => 'nullable|numeric',
            'hips' => 'nullable|numeric',
            'left_thigh' => 'nullable|numeric',
            'right_thigh' => 'nullable|numeric',
            'left_calf' => 'nullable|numeric',
            'right_calf' => 'nullable|numeric',
            // Skinfolds
            'subescapular' => 'nullable|numeric',
            'tricipital' => 'nullable|numeric',
            'bicipital' => 'nullable|numeric',
            'toracica' => 'nullable|numeric',
            'abdominal_fold' => 'nullable|numeric',
            'axilar_media' => 'nullable|numeric',
            'suprailiaca' => 'nullable|numeric',
            'coxa_fold' => 'nullable|numeric',
            'panturrilha_fold' => 'nullable|numeric',
            'sum_skinfolds' => 'nullable|numeric',
            // Additional circumferences
            'ombro' => 'nullable|numeric',
            'torax' => 'nullable|numeric',
            'abdomen_inferior' => 'nullable|numeric',
            'left_arm_contracted' => 'nullable|numeric',
            'right_arm_contracted' => 'nullable|numeric',
            'left_forearm' => 'nullable|numeric',
            'right_forearm' => 'nullable|numeric',
            'left_thigh_proximal' => 'nullable|numeric',
            'left_thigh_medial' => 'nullable|numeric',
            'left_thigh_distal' => 'nullable|numeric',
            'right_thigh_proximal' => 'nullable|numeric',
            'right_thigh_medial' => 'nullable|numeric',
            'right_thigh_distal' => 'nullable|numeric',
            // Methods/results
            'guedes_density' => 'nullable|numeric',
            'guedes_fat_pct' => 'nullable|numeric',
            'guedes_fat_mass' => 'nullable|numeric',
            'guedes_lean_mass' => 'nullable|numeric',
            'pollock3_density' => 'nullable|numeric',
            'pollock3_fat_pct' => 'nullable|numeric',
            'pollock3_fat_mass' => 'nullable|numeric',
            'pollock3_lean_mass' => 'nullable|numeric',
            'pollock7_density' => 'nullable|numeric',
            'pollock7_fat_pct' => 'nullable|numeric',
            'pollock7_fat_mass' => 'nullable|numeric',
            'pollock7_lean_mass' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'photo_front' => 'nullable|image|max:5120', // Max 5MB
            'photo_back' => 'nullable|image|max:5120',
            'photo_side' => 'nullable|image|max:5120',
            'photo_side_right' => 'nullable|image|max:5120',
            'photo_side_left' => 'nullable|image|max:5120',
            'photo_extra' => 'nullable|array|max:9',
            'photo_extra.*' => 'nullable|image|max:5120',
            'replace_extra_photos' => 'nullable|array',
            'replace_extra_photos.*' => 'nullable|image|max:5120',
            'remove_extra_photos' => 'nullable|array',
            'remove_extra_photos.*' => 'nullable|integer|min:0',
            'injuries' => 'nullable|string',
            'medications' => 'nullable|string',
            'surgeries' => 'nullable|string',
            'pain_points' => 'nullable|string',
            'habits' => 'nullable|string',
            'goal' => 'nullable|string',
            'skip_skinfold_protocol' => 'nullable|boolean',
        ]);

        // Adiciona IDs que não vêm do formulário
        $data = $validated;
        $skipSkinfoldProtocol = !empty($data['skip_skinfold_protocol']);
        unset($data['skip_skinfold_protocol']);
        
        // DEBUG: Log TODOS os valores de dobras
        Log::info('DEBUG COMPLETO: Valores de dobras recebidos', [
            'subescapular' => $data['subescapular'] ?? 'null',
            'tricipital' => $data['tricipital'] ?? 'null',
            'bicipital' => $data['bicipital'] ?? 'null',
            'toracica' => $data['toracica'] ?? 'null',
            'abdominal_fold' => $data['abdominal_fold'] ?? 'null',
            'axilar_media' => $data['axilar_media'] ?? 'null',
            'suprailiaca' => $data['suprailiaca'] ?? 'null',
            'coxa_fold' => $data['coxa_fold'] ?? 'null',
            'panturrilha_fold' => $data['panturrilha_fold'] ?? 'null',
            'sum_skinfolds' => $data['sum_skinfolds'] ?? 'null',
        ]);
        
        $data['student_id'] = $student->id;
        $data['professional_id'] = Auth::id();

        $this->validateTotalPhotosLimitOnCreate($request);

        // If sum not provided, calculate sum of available skinfolds
        if (empty($data['sum_skinfolds'])) {
            $sum = 0;
            $keys = ['subescapular','tricipital','bicipital','toracica','abdominal_fold','axilar_media','suprailiaca','coxa_fold','panturrilha_fold'];
            foreach ($keys as $k) {
                if (!empty($data[$k])) {
                    $sum += (float) $data[$k];
                }
            }
            if ($sum > 0) {
                $data['sum_skinfolds'] = $sum;
            }
        }

        // Calcula composição corporal automaticamente
        if ($skipSkinfoldProtocol) {
            $data['selected_protocol'] = null;
            $data['body_fat'] = null;
            $data['muscle_mass'] = null;
            $data['guedes_density'] = null;
            $data['guedes_fat_pct'] = null;
            $data['guedes_fat_mass'] = null;
            $data['guedes_lean_mass'] = null;
            $data['pollock3_density'] = null;
            $data['pollock3_fat_pct'] = null;
            $data['pollock3_fat_mass'] = null;
            $data['pollock3_lean_mass'] = null;
            $data['pollock7_density'] = null;
            $data['pollock7_fat_pct'] = null;
            $data['pollock7_fat_mass'] = null;
            $data['pollock7_lean_mass'] = null;
        } else {
            $this->calculateBodyComposition($data, $student);
        }

        // Upload de Fotos
        if ($request->hasFile('photo_front')) {
            $data['photo_front'] = $request->file('photo_front')->store('assessments', 'private');
        }
        if ($request->hasFile('photo_back')) {
            $data['photo_back'] = $request->file('photo_back')->store('assessments', 'private');
        }
        if ($request->hasFile('photo_side')) {
            $data['photo_side'] = $request->file('photo_side')->store('assessments', 'private');
        }
        if ($request->hasFile('photo_side_right')) {
            $data['photo_side_right'] = $request->file('photo_side_right')->store('assessments', 'private');
        }
        if ($request->hasFile('photo_side_left')) {
            $data['photo_side_left'] = $request->file('photo_side_left')->store('assessments', 'private');
        }
        if ($request->hasFile('photo_extra')) {
            $extraPhotos = [];
            foreach ($request->file('photo_extra') as $extraPhoto) {
                $extraPhotos[] = $extraPhoto->store('assessments', 'private');
            }
            $data['extra_photos'] = $extraPhotos;
        }

        BodyMeasurement::create($data);

        // Se for requisição AJAX, retornar JSON; senão, redirecionar normalmente
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Medidas registradas com sucesso!']);
        }

        return redirect()->route('personal.students.show', $student)->with('success', 'Medidas registradas com sucesso!');
    }

    /**
     * Tela de edição de medida.
     */
    public function editMeasurement(BodyMeasurement $measurement)
    {
        $student = $measurement->student;
        $this->validateStudentBelongsToPersonal($student);
        $previousMeasurement = BodyMeasurement::where('student_id', $student->id)
            ->where('id', '<', $measurement->id)
            ->latest()
            ->first();
        return view('personal.measurements.edit', compact('measurement', 'student', 'previousMeasurement'));
    }

    /**
     * Atualiza medida existente.
     */
    public function updateMeasurement(Request $request, BodyMeasurement $measurement)
    {
        $this->validateStudentBelongsToPersonal($measurement->student);
        
        // Pré-processamento: Trocar vírgula por ponto
        $input = $request->all();
        foreach ($input as $key => $value) {
            if (is_string($value) && is_numeric(str_replace(',', '.', $value))) {
                $input[$key] = str_replace(',', '.', $value);
            }
        }
        $request->replace($input);

        $validated = $request->validate([
            'date' => 'required|date',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'body_fat' => 'nullable|numeric',
            'muscle_mass' => 'nullable|numeric',
            'chest' => 'nullable|numeric',
            'left_arm' => 'nullable|numeric',
            'right_arm' => 'nullable|numeric',
            'waist' => 'nullable|numeric',
            'abdomen' => 'nullable|numeric',
            'hips' => 'nullable|numeric',
            'left_thigh' => 'nullable|numeric',
            'right_thigh' => 'nullable|numeric',
            'left_calf' => 'nullable|numeric',
            'right_calf' => 'nullable|numeric',
            // Skinfolds
            'subescapular' => 'nullable|numeric',
            'tricipital' => 'nullable|numeric',
            'bicipital' => 'nullable|numeric',
            'toracica' => 'nullable|numeric',
            'abdominal_fold' => 'nullable|numeric',
            'axilar_media' => 'nullable|numeric',
            'suprailiaca' => 'nullable|numeric',
            'coxa_fold' => 'nullable|numeric',
            'panturrilha_fold' => 'nullable|numeric',
            'sum_skinfolds' => 'nullable|numeric',
            // Additional circumferences
            'ombro' => 'nullable|numeric',
            'torax' => 'nullable|numeric',
            'abdomen_inferior' => 'nullable|numeric',
            'left_arm_contracted' => 'nullable|numeric',
            'right_arm_contracted' => 'nullable|numeric',
            'left_forearm' => 'nullable|numeric',
            'right_forearm' => 'nullable|numeric',
            'left_thigh_proximal' => 'nullable|numeric',
            'left_thigh_medial' => 'nullable|numeric',
            'left_thigh_distal' => 'nullable|numeric',
            'right_thigh_proximal' => 'nullable|numeric',
            'right_thigh_medial' => 'nullable|numeric',
            'right_thigh_distal' => 'nullable|numeric',
            // Methods/results
            'guedes_density' => 'nullable|numeric',
            'guedes_fat_pct' => 'nullable|numeric',
            'guedes_fat_mass' => 'nullable|numeric',
            'guedes_lean_mass' => 'nullable|numeric',
            'pollock3_density' => 'nullable|numeric',
            'pollock3_fat_pct' => 'nullable|numeric',
            'pollock3_fat_mass' => 'nullable|numeric',
            'pollock3_lean_mass' => 'nullable|numeric',
            'pollock7_density' => 'nullable|numeric',
            'pollock7_fat_pct' => 'nullable|numeric',
            'pollock7_fat_mass' => 'nullable|numeric',
            'pollock7_lean_mass' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'photo_front' => 'nullable|image|max:5120',
            'photo_back' => 'nullable|image|max:5120',
            'photo_side' => 'nullable|image|max:5120',
            'photo_side_right' => 'nullable|image|max:5120',
            'photo_side_left' => 'nullable|image|max:5120',
            'photo_extra' => 'nullable|array|max:9',
            'photo_extra.*' => 'nullable|image|max:5120',
            'injuries' => 'nullable|string',
            'medications' => 'nullable|string',
            'surgeries' => 'nullable|string',
            'pain_points' => 'nullable|string',
            'habits' => 'nullable|string',
            'goal' => 'nullable|string',
            'skip_skinfold_protocol' => 'nullable|boolean',
        ]);

        $data = $validated;
        
        // Verifica flag para pular protocolo (compatibilidade com create e edit)
        // No edit o campo pode vir como no_skinfold_info ou skip_skinfold_protocol
        $skipSkinfoldProtocol = !empty($data['skip_skinfold_protocol']) || $request->has('no_skinfold_info');
        if (isset($data['skip_skinfold_protocol'])) unset($data['skip_skinfold_protocol']);
        if (isset($data['no_skinfold_info'])) unset($data['no_skinfold_info']);

        $this->validateTotalPhotosLimitOnUpdate($request, $measurement);

        // Recalculate sum of skinfolds if not provided
        if (empty($data['sum_skinfolds'])) {
            $sum = 0;
            $keys = ['subescapular','tricipital','bicipital','toracica','abdominal_fold','axilar_media','suprailiaca','coxa_fold','panturrilha_fold'];
            foreach ($keys as $k) {
                if (!empty($data[$k])) {
                    $sum += (float) $data[$k];
                }
            }
            if ($sum > 0) {
                $data['sum_skinfolds'] = $sum;
            }
        }

        // Calcula composição corporal automaticamente
        if ($skipSkinfoldProtocol) {
            $data['selected_protocol'] = null;
            // Se o usuário quiser limpar gordura/massa também, descomente abaixo. 
            // Geralmente se não tem dobras, esses valores calculados não fazem sentido, 
            // mas o usuário pode ter inserido manualmente? 
            // No create nós limpamos. Vamos manter consistência.
            $data['body_fat'] = null;
            $data['muscle_mass'] = null;
            
            $data['guedes_density'] = null;
            $data['guedes_fat_pct'] = null;
            $data['guedes_fat_mass'] = null;
            $data['guedes_lean_mass'] = null;
            $data['pollock3_density'] = null;
            $data['pollock3_fat_pct'] = null;
            $data['pollock3_fat_mass'] = null;
            $data['pollock3_lean_mass'] = null;
            $data['pollock7_density'] = null;
            $data['pollock7_fat_pct'] = null;
            $data['pollock7_fat_mass'] = null;
            $data['pollock7_lean_mass'] = null;
        } else {
            $this->calculateBodyComposition($data, $measurement->student);
        }

        // Upload de Fotos (apaga antiga se enviar nova)
        if ($request->hasFile('photo_front')) {
            $data['photo_front'] = $request->file('photo_front')->store('assessments', 'private');
        }
        if ($request->hasFile('photo_back')) {
            $data['photo_back'] = $request->file('photo_back')->store('assessments', 'private');
        }
        if ($request->hasFile('photo_side')) {
            $data['photo_side'] = $request->file('photo_side')->store('assessments', 'private');
        }
        if ($request->hasFile('photo_side_right')) {
            $data['photo_side_right'] = $request->file('photo_side_right')->store('assessments', 'private');
        }
        if ($request->hasFile('photo_side_left')) {
            $data['photo_side_left'] = $request->file('photo_side_left')->store('assessments', 'private');
        }
        $existingExtraPhotos = is_array($measurement->extra_photos) ? $measurement->extra_photos : [];
        $hasExtraChanges = $request->hasFile('photo_extra') || $request->hasFile('replace_extra_photos') || $request->filled('remove_extra_photos');

        if ($hasExtraChanges) {
            $removeIndexes = collect($request->input('remove_extra_photos', []))
                ->map(fn ($index) => (int) $index)
                ->unique()
                ->values()
                ->all();

            $replaceExtraPhotos = $request->file('replace_extra_photos', []);
            $processedExtraPhotos = [];

            foreach ($existingExtraPhotos as $index => $existingPath) {
                if (in_array((int) $index, $removeIndexes, true)) {
                    continue;
                }

                if (isset($replaceExtraPhotos[$index])) {
                    $processedExtraPhotos[] = $replaceExtraPhotos[$index]->store('assessments', 'private');
                } else {
                    $processedExtraPhotos[] = $existingPath;
                }
            }

            if ($request->hasFile('photo_extra')) {
                foreach ($request->file('photo_extra') as $extraPhoto) {
                    $processedExtraPhotos[] = $extraPhoto->store('assessments', 'private');
                }
            }

            $data['extra_photos'] = array_values($processedExtraPhotos);
        }

        $measurement->update($data);

        // Se for requisição AJAX, retornar JSON; senão, redirecionar normalmente
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Avaliação atualizada com sucesso!']);
        }

        return redirect()->route('personal.students.show', $measurement->student_id)
            ->with('success', 'Avaliação atualizada com sucesso!');
    }

    /**
     * Exclui uma medida.
     */
    public function destroyMeasurement(BodyMeasurement $measurement)
    {
        $this->validateStudentBelongsToPersonal($measurement->student);
        $studentId = $measurement->student_id;
        $measurement->delete();

        return redirect()->route('personal.students.show', $studentId)
            ->with('success', 'Avaliação excluída com sucesso!');
    }

    /**
     * Ativa ou desativa um aluno.
     */
    public function toggleStatus(User $student)
    {
        $this->validateStudentBelongsToPersonal($student);
        // TODO: Verificar se o aluno pertence ao personal (quando tivermos vínculo many-to-many)
        
        $student->is_active = !$student->is_active;
        $student->save();

        $status = $student->is_active ? 'ativado' : 'desativado';
        
        return back()->with('success', "Aluno {$status} com sucesso.");
    }

    /**
     * Redefine a senha de um aluno vinculado ao personal.
     */
    public function resetStudentPassword(Request $request, User $student)
    {
        $this->validateStudentBelongsToPersonal($student);

        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $student->password = Hash::make($validated['password']);
        $student->save();

        return back()->with('success', 'Senha do aluno redefinida com sucesso.');
    }

    /**
     * Calcula composição corporal (Guedes, Pollock 3 e 7) e preenche os campos automaticamente
     */
    private function validateTotalPhotosLimitOnCreate(Request $request): void
    {
        $singlePhotosCount = 0;
        if ($request->hasFile('photo_front')) $singlePhotosCount++;
        if ($request->hasFile('photo_back')) $singlePhotosCount++;
        if ($request->hasFile('photo_side') || $request->hasFile('photo_side_right')) $singlePhotosCount++;
        if ($request->hasFile('photo_side_left')) $singlePhotosCount++;

        $extraPhotosCount = count($request->file('photo_extra', []));
        $totalPhotos = $singlePhotosCount + $extraPhotosCount;

        if ($totalPhotos > 9) {
            throw ValidationException::withMessages([
                'photo_extra' => 'Você pode enviar no máximo 9 imagens no total por avaliação.',
            ]);
        }
    }

    private function validateTotalPhotosLimitOnUpdate(Request $request, BodyMeasurement $measurement): void
    {
        $frontExists = $request->hasFile('photo_front') || !empty($measurement->photo_front);
        $backExists = $request->hasFile('photo_back') || !empty($measurement->photo_back);
        $sideRightExists =
            $request->hasFile('photo_side') ||
            $request->hasFile('photo_side_right') ||
            !empty($measurement->photo_side_right) ||
            !empty($measurement->photo_side);
        $sideLeftExists = $request->hasFile('photo_side_left') || !empty($measurement->photo_side_left);

        $singlePhotosCount = ($frontExists ? 1 : 0)
            + ($backExists ? 1 : 0)
            + ($sideRightExists ? 1 : 0)
            + ($sideLeftExists ? 1 : 0);

        $existingExtraPhotosCount = is_array($measurement->extra_photos)
            ? count($measurement->extra_photos)
            : 0;

        $removeIndexes = collect($request->input('remove_extra_photos', []))
            ->map(fn ($index) => (int) $index)
            ->filter(fn ($index) => $index >= 0 && $index < $existingExtraPhotosCount)
            ->unique()
            ->count();

        $remainingExistingExtraPhotosCount = max(0, $existingExtraPhotosCount - $removeIndexes);
        $newExtraPhotosCount = count($request->file('photo_extra', []));

        $totalPhotos = $singlePhotosCount + $remainingExistingExtraPhotosCount + $newExtraPhotosCount;

        if ($totalPhotos > 9) {
            throw ValidationException::withMessages([
                'photo_extra' => 'Limite máximo atingido: esta avaliação permite até 9 imagens no total.',
            ]);
        }
    }

    private function calculateBodyComposition(&$data, User $student)
    {
        if (empty($data['weight'])) {
            return; // Peso é obrigatório para os cálculos
        }

        $age = BodyCompositionService::calculateAge($student->birth_date);
        if (!$age) {
            return; // Idade é necessária
        }

        $skinfolds = [
            'subescapular' => $data['subescapular'] ?? null,
            'tricipital' => $data['tricipital'] ?? null,
            'bicipital' => $data['bicipital'] ?? null,
            'toracica' => $data['toracica'] ?? null,
            'abdominal_fold' => $data['abdominal_fold'] ?? null,
            'axilar_media' => $data['axilar_media'] ?? null,
            'suprailiaca' => $data['suprailiaca'] ?? null,
            'coxa_fold' => $data['coxa_fold'] ?? null,
            'panturrilha_fold' => $data['panturrilha_fold'] ?? null,
        ];

        $weight = (float) $data['weight'];
        $gender = $student->gender;

        // Calcula Guedes (apenas 3 dobras: subescapular, suprailíaca, coxa)
        $guedesResult = BodyCompositionService::calculateGuedes($weight, $age, $gender, $skinfolds);
        if ($guedesResult) {
            $data['guedes_density'] = $guedesResult['density'];
            $data['guedes_fat_pct'] = $guedesResult['fat_pct'];
            $data['guedes_fat_mass'] = $guedesResult['fat_mass'];
            $data['guedes_lean_mass'] = $guedesResult['lean_mass'];
        }

        // Pollock 3 e 7 dependem de sexo; sem gênero definido, evita fallback incorreto
        if (empty($gender)) {
            $data['pollock3_density'] = null;
            $data['pollock3_fat_pct'] = null;
            $data['pollock3_fat_mass'] = null;
            $data['pollock3_lean_mass'] = null;
            $data['pollock7_density'] = null;
            $data['pollock7_fat_pct'] = null;
            $data['pollock7_fat_mass'] = null;
            $data['pollock7_lean_mass'] = null;

            Log::warning('Cálculo Pollock ignorado: gênero do aluno não informado.', [
                'student_id' => $student->id,
                'student_name' => $student->name,
            ]);

            return;
        }

        // Calcula Pollock 3 (tricipital, suprailíaca, coxa - apenas estas 3 dobras!)
        // IMPORTANTE: NÃO inclui panturrilha ou outras dobras
        $pollock3Result = BodyCompositionService::calculatePollock3($weight, $age, $gender, $skinfolds);
        if ($pollock3Result) {
            $data['pollock3_density'] = $pollock3Result['density'];
            $data['pollock3_fat_pct'] = $pollock3Result['fat_pct'];
            $data['pollock3_fat_mass'] = $pollock3Result['fat_mass'];
            $data['pollock3_lean_mass'] = $pollock3Result['lean_mass'];
        }

        // Calcula Pollock 7
        $pollock7Result = BodyCompositionService::calculatePollock7($weight, $age, $gender, $skinfolds);
        if ($pollock7Result) {
            $data['pollock7_density'] = $pollock7Result['density'];
            $data['pollock7_fat_pct'] = $pollock7Result['fat_pct'];
            $data['pollock7_fat_mass'] = $pollock7Result['fat_mass'];
            $data['pollock7_lean_mass'] = $pollock7Result['lean_mass'];
        }
    }

    public function evolutionIndex()
    {
        $user = Auth::user();
        $students = $user->students()->orderBy('users.name')->get(['users.id', 'users.name']);
        return view('personal.evolution.index', compact('students'));
    }

    public function evolutionData(User $student)
    {
        $this->validateStudentBelongsToPersonal($student);

        $measurements = BodyMeasurement::where('student_id', $student->id)
            ->orderBy('date')
            ->get(['date', 'weight', 'muscle_mass', 'body_fat', 'height', 'waist']);

        $dates    = $measurements->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m/Y'))->values();
        $weights  = $measurements->pluck('weight')->values();
        $muscles  = $measurements->pluck('muscle_mass')->values();
        $bodyFats = $measurements->pluck('body_fat')->values();

        $first    = $measurements->first();
        $last     = $measurements->last();
        $previous = $measurements->count() >= 2 ? $measurements->slice(-2, 1)->first() : null;

        $weightDiff = ($previous && $previous->weight    && $last->weight)      ? round($last->weight      - $previous->weight,      1) : null;
        $muscleDiff = ($previous && $previous->muscle_mass && $last->muscle_mass) ? round($last->muscle_mass - $previous->muscle_mass,  1) : null;
        $fatDiff    = ($previous && $previous->body_fat  && $last->body_fat)    ? round($last->body_fat    - $previous->body_fat,      2) : null;

        $summary = null;
        if ($first && $last && $measurements->count() >= 2) {
            $summary = [
                'total'        => $measurements->count(),
                'weight'       => ['value' => $last->weight,       'diff' => $weightDiff],
                'muscle_mass'  => ['value' => $last->muscle_mass,  'diff' => $muscleDiff],
                'body_fat'     => ['value' => $last->body_fat,     'diff' => $fatDiff],
            ];
        }

        $history = $measurements->reverse()->map(function ($m) {
            $imc = ($m->weight && $m->height) ? round($m->weight / (($m->height / 100) ** 2), 1) : null;
            return [
                'date'        => $m->date ? \Carbon\Carbon::parse($m->date)->format('d/m/Y') : null,
                'weight'      => $m->weight      ? number_format($m->weight,      1, ',', '.') : null,
                'muscle_mass' => $m->muscle_mass ? number_format($m->muscle_mass, 1, ',', '.') : null,
                'body_fat'    => $m->body_fat    ? number_format($m->body_fat,    1, ',', '.') : null,
                'imc'         => $imc            ? number_format($imc,            1, ',', '.') : null,
                'waist'       => $m->waist       ? number_format($m->waist,       1, ',', '.') : null,
            ];
        })->values();

        return response()->json([
            'student'  => ['id' => $student->id, 'name' => $student->name],
            'dates'    => $dates,
            'weights'  => $weights,
            'muscles'  => $muscles,
            'bodyFats' => $bodyFats,
            'summary'  => $summary,
            'history'  => $history,
        ]);
    }
}
