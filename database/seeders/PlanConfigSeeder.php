<?php

namespace Database\Seeders;

use App\Models\PlanConfig;
use Illuminate\Database\Seeder;

class PlanConfigSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'plan_id'      => 'plan_starter',
                'name'         => 'Starter',
                'price'        => 3.00,
                'max_students' => 15,
                'color'        => '#3b82f6',
                'is_active'    => true,
                'features'     => [
                    'Até 15 alunos ativos',
                    'Prescrição de treinos completa',
                    'Medidas e avaliações corporais',
                    'Acompanhamento de evolução com gráficos',
                    'App exclusivo para o aluno',
                    'Geração de treinos com IA',
                    'Avaliação postural com IA',
                    'Suporte por e-mail',
                ],
            ],
            [
                'plan_id'      => 'plan_pro',
                'name'         => 'Pro',
                'price'        => 4.00,
                'max_students' => 50,
                'color'        => '#8b5cf6',
                'is_active'    => true,
                'features'     => [
                    'Até 50 alunos ativos',
                    'Prescrição de treinos completa',
                    'Medidas e avaliações corporais',
                    'Acompanhamento de evolução com gráficos',
                    'App exclusivo para o aluno',
                    'Controle financeiro dos alunos',
                    'Geração de treinos com IA',
                    'Avaliação postural com IA',
                    'Relatórios em PDF e Excel',
                    'Suporte prioritário',
                ],
            ],
            [
                'plan_id'      => 'plan_elite',
                'name'         => 'Elite',
                'price'        => 5.00,
                'max_students' => 100,
                'color'        => '#f59e0b',
                'is_active'    => true,
                'features'     => [
                    'A partir de 100 alunos ativos',
                    'Prescrição de treinos completa',
                    'Medidas e avaliações corporais',
                    'Acompanhamento de evolução com gráficos',
                    'App exclusivo para o aluno',
                    'Controle financeiro dos alunos',
                    'Geração de treinos com IA',
                    'Avaliação postural com IA',
                    'Relatórios em PDF e Excel',
                    'Suporte VIP exclusivo',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            PlanConfig::updateOrCreate(
                ['plan_id' => $plan['plan_id']],
                $plan
            );
        }
    }
}
