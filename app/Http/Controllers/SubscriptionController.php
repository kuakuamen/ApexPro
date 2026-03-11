<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Rules\Cpf;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    protected $plans = [
        'plan_10' => [
            'id' => 'plan_10',
            'name' => 'Plano Iniciante',
            'price' => 49.90,
            'max_students' => 10,
            'color' => '#10b981', // green
            'features' => [
                'Gerencie até 10 alunos',
                'Organize e prescreva treinos',
                'Gere treinos instantâneos com IA',
                'Avalie a postura via IA'
            ]
        ],
        'plan_50' => [
            'id' => 'plan_50',
            'name' => 'Plano Profissional',
            'price' => 69.90,
            'max_students' => 50,
            'color' => '#3b82f6', // blue
            'features' => [
                'Gerencie até 50 alunos',
                'Organize e prescreva treinos',
                'Gere treinos instantâneos com IA',
                'Avalie a postura via IA',
                'Tenha suporte prioritário',
                'Controle o financeiro dos alunos'
            ]
        ],
        'plan_100' => [
            'id' => 'plan_100',
            'name' => 'Plano Elite',
            'price' => 99.99,
            'max_students' => 100,
            'color' => '#8b5cf6', // purple
            'features' => [
                'Gerencie até 100 alunos',
                'Organize e prescreva treinos',
                'Gere treinos instantâneos com IA',
                'Avalie a postura via IA',
                'Tenha suporte prioritário',
                'Controle o financeiro dos alunos',
                'Acesse suporte VIP exclusivo'
            ]
        ],
        'plan_500' => [
            'id' => 'plan_500',
            'name' => 'Plano Studio',
            'price' => 299.00,
            'max_students' => 500,
            'color' => '#f59e0b', // amber
            'features' => [
                'Gerencie até 500 alunos',
                'Gerencie todo o seu negócio',
                'Organize e prescreva treinos',
                'Analise a evolução visual com IA',
                'Gere treinos instantâneos com IA',
                'Acesse suporte VIP exclusivo',
                'Acompanhe relatórios detalhados'
            ]
        ]
    ];

    /**
     * Exibe a página de planos.
     */
    public function index()
    {
        return view('plans.index', ['plans' => $this->plans]);
    }

    /**
     * Exibe a página de checkout (pagamento).
     */
    public function checkout($planId)
    {
        if (!isset($this->plans[$planId])) {
            abort(404, 'Plano não encontrado');
        }

        return view('plans.checkout', ['plan' => $this->plans[$planId]]);
    }

    /**
     * Processa o pagamento (Mock) e redireciona para o cadastro.
     */
    public function processPayment(Request $request, $planId)
    {
        if (!isset($this->plans[$planId])) {
            abort(404);
        }

        // Simulação de pagamento aprovado
        // Armazena o plano escolhido na sessão para o cadastro
        session(['selected_plan' => $planId]);
        session(['payment_confirmed' => true]);

        return redirect()->route('subscription.register');
    }

    /**
     * Exibe o formulário de cadastro do personal após pagamento.
     */
    public function showRegisterForm()
    {
        if (!session('payment_confirmed') || !session('selected_plan')) {
            return redirect()->route('plans.index')->with('error', 'Selecione um plano primeiro.');
        }

        $planId = session('selected_plan');
        $plan = $this->plans[$planId];

        return view('auth.register-personal', compact('plan'));
    }

    /**
     * Processa o cadastro do personal.
     */
    public function storePersonal(Request $request)
    {
        if (!session('payment_confirmed') || !session('selected_plan')) {
            abort(403, 'Pagamento não confirmado.');
        }

        $planId = session('selected_plan');
        $plan = $this->plans[$planId];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'cpf' => ['required', 'string', 'unique:users', new Cpf],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:20'], // Whatsapp
            'address' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'cref' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cpf' => preg_replace('/[^0-9]/', '', $validated['cpf']),
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'profession' => $validated['profession'],
            'cref' => $validated['cref'],
            'password' => Hash::make($validated['password']),
            'role' => 'personal',
            'is_active' => true,
            'plan_name' => $plan['name'],
            'max_students' => $plan['max_students'],
            'subscription_expires_at' => Carbon::now()->addDays(30),
        ]);

        // Limpar sessão
        session()->forget(['selected_plan', 'payment_confirmed']);

        Auth::login($user);

        return redirect()->route('personal.dashboard');
    }

    /**
     * Exibe tela de renovação.
     */
    public function showRenew()
    {
        return view('subscription.renew', ['plans' => $this->plans]);
    }

    /**
     * Exibe o checkout de renovação.
     */
    public function renewCheckout($planId)
    {
        if (!isset($this->plans[$planId])) {
            abort(404, 'Plano não encontrado');
        }

        return view('plans.checkout', [
            'plan' => $this->plans[$planId],
            'isRenewal' => true
        ]);
    }

    /**
     * Processa renovação.
     */
    public function processRenew(Request $request, $planId)
    {
         if (!isset($this->plans[$planId])) {
            abort(404);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $plan = $this->plans[$planId];

        // Atualiza assinatura
        $user->update([
            'subscription_expires_at' => Carbon::now()->addDays(30),
            'plan_name' => $plan['name'],
            'max_students' => $plan['max_students'],
            'is_active' => true, // Reativa se estiver desativado
        ]);

        return redirect()->route('personal.dashboard')->with('success', 'Assinatura renovada com sucesso!');
    }
}
