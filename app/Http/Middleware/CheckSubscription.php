<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfessionalStudent;
use App\Models\StudentPlan;

class CheckSubscription
{
    protected array $allowedRoutes = [
        'subscription.renew',
        'subscription.renew.checkout',
        'subscription.renew.process',
        'subscription.pix-waiting',
        'subscription.payment-result',
        'subscription.status',
        'subscription.history',
        'plans.*',
        'logout',
        'webhook.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        if ($user->role === 'admin') {
            return $next($request);
        }

        if ($user->role === 'personal') {
            // Verificar se rota é permitida mesmo expirado
            foreach ($this->allowedRoutes as $pattern) {
                if ($request->routeIs($pattern)) {
                    return $next($request);
                }
            }

            $subscription = $user->professionalSubscription;

            // Sem assinatura ou suspensa/cancelada → renovação
            if (!$subscription || in_array($subscription->status, ['suspended', 'cancelled'])) {
                return redirect()->route('subscription.renew');
            }

            // Assinatura pendente (aguardando pagamento)
            if ($subscription->status === 'pending') {
                return redirect()->route('subscription.renew');
            }

            // Ativa → tudo ok
            if ($subscription->isActive()) {
                return $next($request);
            }

            // Em grace period → acesso com warning
            if ($subscription->isInGrace()) {
                session(['subscription_grace_warning' => true]);
                return $next($request);
            }

            // Expirado (grace venceu)
            return redirect()->route('subscription.renew');
        }

        if ($user->role === 'aluno') {
            // Bloqueio por inadimplência financeira
            $isSuspended = StudentPlan::where('student_id', $user->id)
                ->where('status', 'suspended')
                ->exists();

            if ($isSuspended) {
                if (!$request->routeIs('logout') && !$request->routeIs('login')) {
                    Auth::logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Seu acesso está suspenso por inadimplência. Entre em contato com seu personal trainer.'
                    ]);
                }
            }

            // Bloqueio por expiração da assinatura do personal
            $link = ProfessionalStudent::where('student_id', $user->id)
                ->where('type', 'personal')
                ->with('professional')
                ->first();

            if ($link && $link->professional) {
                $personal = $link->professional;
                if ($personal->subscription_expires_at && $personal->subscription_expires_at->isPast()) {
                    if (!$request->routeIs('logout') && !$request->routeIs('login')) {
                        Auth::logout();
                        return redirect()->route('login')->withErrors([
                            'email' => 'O acesso ao sistema foi suspenso temporariamente. O plano do seu Personal Trainer expirou.'
                        ]);
                    }
                }
            }
        }

        return $next($request);
    }
}
