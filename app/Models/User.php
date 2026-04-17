<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

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
        'address_cep',
        'address_state',
        'address_city',
        'address_street',
        'address_neighborhood',
        'address_number',
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
        'cref',
        'max_students',
        'subscription_expires_at',
        'trial_ends_at',
        'plan_name',
        'mp_customer_id',
        'assessment_frequency',
        'profile_photo_path',
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
            'subscription_expires_at' => 'datetime',
            'trial_ends_at'           => 'datetime',
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

    public function financialPlans()
    {
        return $this->hasMany(FinancialPlan::class, 'personal_id');
    }

    public function studentPlan()
    {
        return $this->hasOne(StudentPlan::class, 'student_id');
    }

    public function professionalSubscription()
    {
        return $this->hasOne(ProfessionalSubscription::class);
    }

    public function subscriptionTransactions()
    {
        return $this->hasMany(SubscriptionTransaction::class);
    }

    public function hasActiveSubscription(): bool
    {
        $sub = $this->professionalSubscription;
        if (!$sub) return false;
        return $sub->canAccessPlatform();
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (empty($this->profile_photo_path)) {
            return null;
        }

        return asset('storage/' . $this->profile_photo_path);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
