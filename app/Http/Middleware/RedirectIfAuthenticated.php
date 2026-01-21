<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Impede usuários já autenticados de acessarem rotas de guest (ex.: login/registro).
     *
     * Se qualquer guard informado estiver autenticado, redireciona para HOME; caso
     * contrário, segue o fluxo normal do middleware.
     *
     * @param Request $request A requisição atual.
     * @param Closure $next Próxima etapa do pipeline.
     * @param string ...$guards Guards a serem checados (opcional).
     * @return Response Redireciona autenticados ou permite continuidade para guests.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
