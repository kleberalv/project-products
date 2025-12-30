<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->tokens()->exists()) {
            // Verificar o token sendo utilizado na requisição
            $currentToken = $user->currentAccessToken();
            
            // Se o token atual está deletado/revogado, rejeitar
            if ($currentToken && ($currentToken->deleted_at !== null || $currentToken->revoked_at !== null)) {
                // Diferenciar resposta para API e Web
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['message' => 'Token revogado ou expirado'], 419);
                }
                
                Auth::guard('web')->logout();
                return redirect('/login')->with('error', 'Seu token foi revogado ou expirou.');
            }

            // Verificar se há algum token com expiração válida (ignorando tokens deletados)
            $hasValidToken = $user->tokens()
                ->whereNull('deleted_at')  // Ignorar soft-deleted
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                })
                ->exists();

            if (!$hasValidToken) {
                // Revogar todos os tokens (não deletados) e preservar histórico com soft delete
                $user->tokens()->whereNull('deleted_at')->update([
                    'revoked_at' => now(),
                    'deleted_at' => now(),
                ]);

                // Diferenciar resposta para API e Web
                if ($request->expectsJson() || $request->is('api/*')) {
                    // Para API, retornar 419 (Page Expired)
                    return response()->json(['message' => 'Sessão expirada'], 419);
                }

                // Para Web, efetuar logout de sessão
                Auth::guard('web')->logout();
                return redirect('/login')->with('error', 'Sua sessão expirou. Por favor, faça login novamente.');
            }

            // Atualizar last_used_at
            if ($request->expectsJson() || $request->is('api/*')) {
                // API: atualizar token atual
                if ($currentToken) {
                    $currentToken->update(['last_used_at' => now()]);
                }
            } else {
                // Web: atualizar last_used_at de todos os tokens não deletados
                $user->tokens()->whereNull('deleted_at')->update(['last_used_at' => now()]);
            }
        }

        return $next($request);
    }
}
