<?php
require 'bootstrap/app.php';
$app = app();

$user = App\Models\User::where('email', 'admin@fitmanager.com')->first();
if ($user) {
    echo "Usuário encontrado: {$user->name} - Role: {$user->role}\n";
} else {
    echo "Criando usuário admin...\n";
    App\Models\User::create([
        'name' => 'Administrador',
        'email' => 'admin@fitmanager.com',
        'password' => bcrypt('Admin@123456'),
        'role' => 'admin',
        'is_active' => true,
    ]);
    echo "✓ Usuário criado com sucesso!\n";
}
