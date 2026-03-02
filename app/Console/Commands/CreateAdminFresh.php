<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminFresh extends Command
{
    protected $signature = 'admin:create-fresh';
    protected $description = 'Delete and recreate admin user from scratch';

    public function handle()
    {
        // Deletar admin existente
        $deleted = User::where('email', 'admin@fitmanager.com')->delete();
        if ($deleted) {
            $this->info('✓ Admin antigo deletado');
        }
        
        // Criar novo
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@fitmanager.com',
            'password' => bcrypt('Admin@123456'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        
        $this->info('✓ Admin criado com sucesso!');
        $this->warn('Email: admin@fitmanager.com');
        $this->warn('Senha: Admin@123456');
        $this->warn('Role: ' . $admin->role);
    }
}
