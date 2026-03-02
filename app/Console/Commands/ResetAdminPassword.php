<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset-password';
    protected $description = 'Reset admin password to Admin@123456';

    public function handle()
    {
        $admin = User::where('email', 'admin@fitmanager.com')->first();
        
        if (!$admin) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@fitmanager.com',
                'password' => bcrypt('Admin@123456'),
                'role' => 'admin',
                'is_active' => true,
            ]);
            $this->info('✓ Usuário admin criado com sucesso!');
        } else {
            $admin->update([
                'password' => bcrypt('Admin@123456'),
                'is_active' => true,
            ]);
            $this->info('✓ Senha do admin resetada com sucesso!');
        }
        
        $this->warn('Email: admin@fitmanager.com');
        $this->warn('Senha: Admin@123456');
    }
}
