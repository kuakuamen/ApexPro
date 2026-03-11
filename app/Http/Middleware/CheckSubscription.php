<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfessionalStudent;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Se for admin, não bloqueia
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Se for Personal
        if ($user->role === 'personal') {
            if ($user->subscription_expires_at && $user->subscription_expires_at->isPast()) {
                // Se não estiver na rota de renovação, redireciona
                if (!$request->routeIs('subscription.renew') && !$request->routeIs('plans.process') && !$request->routeIs('logout')) {
                    return redirect()->route('subscription.renew');
                }
            }
        }

        // Se for Aluno
        if ($user->role === 'aluno') {
            // Verificar o personal do aluno
            // O aluno tem um professional_students onde professional_id é o personal
            // Mas a relação no model User é professionalStudents() que retorna hasMany
            // Precisamos encontrar o vinculo onde type='personal' e student_id = user->id
            
            $link = ProfessionalStudent::where('student_id', $user->id)
                ->where('type', 'personal')
                ->with('professional')
                ->first();

            if ($link && $link->professional) {
                $personal = $link->professional;
                if ($personal->subscription_expires_at && $personal->subscription_expires_at->isPast()) {
                     // Bloqueia acesso do aluno se o personal estiver inadimplente
                     if (!$request->routeIs('logout') && !$request->routeIs('login')) {
                        Auth::logout();
                        return redirect()->route('login')->withErrors(['email' => 'O acesso ao sistema foi suspenso temporariamente. O plano do seu Personal Trainer expirou.']);
                     }
                }
            }
        }

        return $next($request);
    }
}
