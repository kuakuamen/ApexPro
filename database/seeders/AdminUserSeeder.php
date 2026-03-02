<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se admin já existe
        if (User::where('email', 'admin@fitmanager.com')->exists()) {
            $this->command->info('Usuário admin já existe!');
            return;
        }

        User::create([
            'name' => 'Administrador',
            'email' => 'admin@fitmanager.com',
            'password' => bcrypt('Admin@123456'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->command->info('Usuário admin criado com sucesso!');
        $this->command->warn('Email: admin@fitmanager.com');
        $this->command->warn('Senha: Admin@123456');
    }
}
