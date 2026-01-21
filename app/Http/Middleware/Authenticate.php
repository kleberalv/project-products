<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Retorna a rota de redirecionamento quando o usuário não está autenticado.
     *
     * Para requisições JSON (API) retorna null para evitar redirecionamento e permitir
     * que a API responda com 401. Para requisições Web retorna a rota nomeada 'login'.
     *
     * @param Request $request A requisição atual para avaliar o tipo de resposta esperado.
     * @return string|null A URL de login para fluxos Web ou null para fluxos API/JSON.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
