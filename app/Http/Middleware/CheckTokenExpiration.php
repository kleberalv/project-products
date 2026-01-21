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
            $currentToken = $user->currentAccessToken();
            
            if ($currentToken && ($currentToken->deleted_at !== null || $currentToken->revoked_at !== null)) {
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
                $user->tokens()->whereNull('deleted_at')->update([
                    'revoked_at' => now(),
                    'deleted_at' => now(),
                ]);

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
