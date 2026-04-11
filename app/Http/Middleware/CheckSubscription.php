<?php

namespace App\Http\Middleware;

use App\Models\ProfessionalStudent;
use App\Models\StudentPlan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

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
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        if ($user->role === 'admin') {
            return $next($request);
        }

        if ($user->role === 'personal') {
            foreach ($this->allowedRoutes as $pattern) {
                if ($request->routeIs($pattern)) {
                    return $next($request);
                }
            }

            $subscription = $user->professionalSubscription;

            if (!$subscription || $subscription->status === 'suspended') {
                return redirect()->route('subscription.renew');
            }

            if ($subscription->canAccessPlatform()) {
                return $next($request);
            }

            return redirect()->route('subscription.renew');
        }

        if ($user->role === 'aluno') {
            $isSuspended = StudentPlan::where('student_id', $user->id)
                ->where('status', 'suspended')
                ->exists();

            if ($isSuspended) {
                if (!$request->routeIs('logout') && !$request->routeIs('login')) {
                    Auth::logout();

                    return redirect()->route('login')->withErrors([
                        'email' => 'Seu acesso esta suspenso por inadimplencia. Entre em contato com seu personal trainer.',
                    ]);
                }
            }

            $link = ProfessionalStudent::where('student_id', $user->id)
                ->where('type', 'personal')
                ->with('professional')
                ->first();

            if ($link && $link->professional) {
                $personal = $link->professional;
                $personalSubscription = $personal->professionalSubscription;

                $personalHasAccess = $personalSubscription
                    ? $personalSubscription->canAccessPlatform()
                    : !($personal->subscription_expires_at && $personal->subscription_expires_at->isPast());

                if (!$personalHasAccess) {
                    if (!$request->routeIs('logout') && !$request->routeIs('login')) {
                        Auth::logout();

                        return redirect()->route('login')->withErrors([
                            'email' => 'O acesso ao sistema foi suspenso temporariamente. O plano do seu Personal Trainer expirou.',
                        ]);
                    }
                }
            }
        }

        return $next($request);
    }
}
