<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar Personal
        User::firstOrCreate(
            ['email' => 'personal@teste.com'],
            [
                'name' => 'Personal Trainer Teste',
                'password' => Hash::make('password'),
                'role' => 'personal',
            ]
        );

        // Criar Nutricionista
        User::firstOrCreate(
            ['email' => 'nutri@teste.com'],
            [
                'name' => 'Nutricionista Teste',
                'password' => Hash::make('password'),
                'role' => 'nutri',
            ]
        );

        // Criar Aluno
        User::firstOrCreate(
            ['email' => 'aluno@teste.com'],
            [
                'name' => 'Aluno Teste',
                'password' => Hash::make('password'),
                'role' => 'aluno',
            ]
        );
    }
}
