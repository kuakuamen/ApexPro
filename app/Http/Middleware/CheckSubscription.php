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
            if ($user->subscription_expires_at && $user->subscription_expires_at->isPast()) {
                if (!$request->routeIs('subscription.renew') && !$request->routeIs('plans.process') && !$request->routeIs('logout')) {
                    return redirect()->route('subscription.renew');
                }
            }
        }

        if ($user->role === 'aluno') {
            // Bloqueio por inadimplência financeira (módulo financeiro do personal)
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
