<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ProfessionalStudent;
use Illuminate\Console\Command;

class DebugStudentLink extends Command
{
    protected $signature = 'debug:student-link';
    protected $description = 'Debug student link for Mateus Borges';

    public function handle()
    {
        $this->line('=== Procurando Eduardo ===');
        $eduardo = User::where('name', 'like', '%eduardo%')->orWhere('email', 'like', '%eduardo%')->first();
        
        if (!$eduardo) {
            $this->error('Eduardo não encontrado');
            $this->line('Usuários no banco:');
            User::all()->each(fn($u) => $this->line("  - {$u->name} ({$u->email}) - Role: {$u->role}"));
            return;
        }
        
        $this->info("✓ Eduardo encontrado: {$eduardo->name} (ID: {$eduardo->id}, Role: {$eduardo->role})");

        $this->line("\n=== Procurando Mateus ===");
        $mateus = User::where('email', 'mateus_borges2001@hotmail.com')->first();
        
        if (!$mateus) {
            $this->error('Mateus não encontrado');
            return;
        }
        
        $this->info("✓ Mateus encontrado: {$mateus->name} (ID: {$mateus->id}, Role: {$mateus->role})");

        $this->line("\n=== Verificando Vínculo ===");
        $link = ProfessionalStudent::where('professional_id', $eduardo->id)
            ->where('student_id', $mateus->id)
            ->first();

        if ($link) {
            $this->info("✓ Vínculo encontrado!");
            $this->line("  - Type: {$link->type}");
            $this->line("  - Created at: {$link->created_at}");
        } else {
            $this->error("✗ NÃO há vínculo entre Eduardo e Mateus");
            
            $this->line("\n--- Alunos vinculados a Eduardo ---");
            $eduardoStudents = ProfessionalStudent::where('professional_id', $eduardo->id)->get();
            $this->info("Total: " . $eduardoStudents->count());
            foreach ($eduardoStudents as $link) {
                $student = User::find($link->student_id);
                $this->line("  - {$student->name} (ID: {$student->id}, Type: {$link->type})");
            }
        }

        $this->line("\n=== Teste de Query do Dashboard ===");
        $students = $eduardo->students()->get();
        $this->info("Alunos retornados pela relação students(): " . $students->count());
        foreach ($students as $student) {
            $this->line("  - {$student->name}");
        }
    }
}
