<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiration
{
    /**
     * Valida o estado do token Sanctum antes de continuar a requisição.
     *
     * Rejeita tokens revogados ou expirados com 419 em fluxos API/JSON ou redireciona
     * para login no fluxo Web. Para tokens válidos, atualiza `last_used_at` para manter
     * o controle de atividade.
     *
     * @param Request $request A requisição atual (usada para decidir resposta JSON ou redirect).
     * @param Closure $next Próxima etapa do pipeline de middleware.
     * @return Response Resposta final (pode ser JSON 419 ou redirect se inválido).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->tokens()->exists()) {
            $currentToken = $user->currentAccessToken();
            
            if ($currentToken && $currentToken->deleted_at !== null) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['message' => 'Token revogado ou expirado'], 419);
                }
                
                Auth::guard('web')->logout();
                return redirect('/login')->with('error', 'Seu token foi revogado ou expirou.');
            }

            $hasValidToken = $user->tokens()
                ->whereNull('deleted_at')
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                })
                ->exists();

            if (!$hasValidToken) {
                $user->tokens()->whereNull('deleted_at')->delete();

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['message' => 'Sessão expirada'], 419);
                }

                Auth::guard('web')->logout();
                return redirect('/login')->with('error', 'Sua sessão expirou. Por favor, faça login novamente.');
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                if ($currentToken) {
                    $currentToken->update(['last_used_at' => now()]);
                }
            } else {
                $user->tokens()->whereNull('deleted_at')->update(['last_used_at' => now()]);
            }
        }

        return $next($request);
    }
}
