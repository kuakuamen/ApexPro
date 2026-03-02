<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FixAdminRole extends Command
{
    protected $signature = 'admin:fix-role';
    protected $description = 'Fix admin user role';

    public function handle()
    {
        $admin = User::where('email', 'admin@fitmanager.com')->first();
        
        if (!$admin) {
            $this->error('Admin não encontrado!');
            return;
        }
        
        $this->info('Usuário encontrado:');
        $this->info('  Nome: ' . $admin->name);
        $this->info('  Email: ' . $admin->email);
        $this->info('  Role atual: ' . $admin->role);
        $this->info('  Ativo: ' . ($admin->is_active ? 'Sim' : 'Não'));
        
        // Atualizar para admin
        $admin->update([
            'role' => 'admin',
            'is_active' => true,
        ]);
        
        $this->info('✓ Role atualizado para admin!');
    }
}
