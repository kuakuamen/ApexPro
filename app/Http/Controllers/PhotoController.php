<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\BodyMeasurement;
use App\Models\ProfessionalStudent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PhotoController extends Controller
{
    private function hasProfessionalAccess($user, int $studentId): bool
    {
        if (!in_array($user->role, ['personal', 'nutri'], true)) {
            return false;
        }

        return ProfessionalStudent::where('student_id', $studentId)
            ->where('professional_id', $user->id)
            ->exists();
    }

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
        $isProfessional = $this->hasProfessionalAccess($user, $assessment->student_id);

        if (!$isStudent && !$isProfessional) {
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

        return response()->file(Storage::disk('private')->path($path), [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
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
        $isProfessional = $this->hasProfessionalAccess($user, $measurement->student_id);

        if (!$isStudent && !$isProfessional) {
            abort(403, 'Você não tem permissão para acessar esta imagem');
        }

        // Determinar qual arquivo buscar
        $path = match($type) {
            'front' => $measurement->photo_front,
            'side' => $measurement->photo_side,
            'side_right' => $measurement->photo_side_right ?: $measurement->photo_side,
            'side_left' => $measurement->photo_side_left,
            'back' => $measurement->photo_back,
            default => abort(404, 'Tipo de foto inválido')
        };

        if (!$path || !Storage::disk('private')->exists($path)) {
            abort(404, 'Foto não encontrada');
        }

        return response()->file(Storage::disk('private')->path($path), [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Serve fotos extras de medição corporal (BodyMeasurement) com validação de segurança
     */
    public function showMeasurementExtra($measurementId, $index)
    {
        $measurement = BodyMeasurement::find($measurementId);
        if (!$measurement) {
            abort(404, 'Medição não encontrada');
        }

        $user = Auth::user();
        $isStudent = $user->id === $measurement->student_id;
        $isProfessional = $this->hasProfessionalAccess($user, $measurement->student_id);

        if (!$isStudent && !$isProfessional) {
            abort(403, 'Você não tem permissão para acessar esta imagem');
        }

        $extraPhotos = is_array($measurement->extra_photos) ? $measurement->extra_photos : [];
        $path = $extraPhotos[(int) $index] ?? null;

        if (!$path || !Storage::disk('private')->exists($path)) {
            abort(404, 'Foto não encontrada');
        }

        return response()->file(Storage::disk('private')->path($path), [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
