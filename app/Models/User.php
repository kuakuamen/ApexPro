<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'cpf',
        'address',
        'phone',
        'birth_date',
        'gender',
        'profession',
        'password',
        'role',
        'is_active',
        'license_expires_at',
        'license_active',
        'admin_notes',
        // 'injuries', 'medications', 'surgeries', 'availability_time', 'frequency' // Removidos conforme solicitado
    ];
// ... (resto do arquivo igual)
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'license_active' => 'boolean',
            'license_expires_at' => 'date',
        ];
    }
    
    // ... métodos existentes ...
    public function workoutPlans()
    {
        return $this->hasMany(WorkoutPlan::class, 'student_id');
    }

    public function dietPlans()
    {
        return $this->hasMany(DietPlan::class, 'student_id');
    }

    public function measurements()
    {
        return $this->hasMany(BodyMeasurement::class, 'student_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'student_id');
    }
    
    /**
     * Alunos de um personal (para role 'personal')
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'professional_students', 'professional_id', 'student_id')
            ->withTimestamps();
    }

    /**
     * Registros de vínculo como personal
     */
    public function professionalStudents()
    {
        return $this->hasMany(ProfessionalStudent::class, 'professional_id');
    }
}
