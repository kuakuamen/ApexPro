<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\BodyMeasurement;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PhotoController extends Controller
{
    /**
     * Serve fotos de avaliação (Assessment) com validação de segurança
     */
    public function show($assessmentId, $type)
    {
        // Verificar se assessment existe
        $assessment = Assessment::find($assessmentId);
        if (!$assessment) {
            abort(404, 'Avaliação não encontrada');
        }

        // Verificar permissões: apenas o aluno e seu personal podem ver
        $user = Auth::user();
        $isStudent = $user->id === $assessment->student_id;
        $isPersonal = $user->role === 'personal' && 
                      $assessment->student->professionalStudents()
                          ->where('professional_id', $user->id)
                          ->exists();

        if (!$isStudent && !$isPersonal) {
            abort(403, 'Você não tem permissão para acessar esta imagem');
        }

        // Determinar qual arquivo buscar
        $path = match($type) {
            'front' => $assessment->front_image_path,
            'side' => $assessment->side_image_path,
            'back' => $assessment->back_image_path,
            default => abort(404, 'Tipo de foto inválido')
        };

        if (!$path || !Storage::disk('private')->exists($path)) {
            abort(404, 'Foto não encontrada');
        }

        return response()->file(Storage::disk('private')->path($path));
    }

    /**
     * Serve fotos de medição corporal (BodyMeasurement) com validação de segurança
     */
    public function showMeasurement($measurementId, $type)
    {
        // Verificar se medição existe
        $measurement = BodyMeasurement::find($measurementId);
        if (!$measurement) {
            abort(404, 'Medição não encontrada');
        }

        // Verificar permissões: apenas o aluno e seu personal podem ver
        $user = Auth::user();
        $isStudent = $user->id === $measurement->student_id;
        $isPersonal = $user->role === 'personal' && 
                      $measurement->student->professionalStudents()
                          ->where('professional_id', $user->id)
                          ->exists();

        if (!$isStudent && !$isPersonal) {
            abort(403, 'Você não tem permissão para acessar esta imagem');
        }

        // Determinar qual arquivo buscar
        $path = match($type) {
            'front' => $measurement->photo_front,
            'side' => $measurement->photo_side,
            'back' => $measurement->photo_back,
            default => abort(404, 'Tipo de foto inválido')
        };

        if (!$path || !Storage::disk('private')->exists($path)) {
            abort(404, 'Foto não encontrada');
        }

        return response()->file(Storage::disk('private')->path($path));
    }
}
