<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Rotas isentas de verificação CSRF.
     *
     * Mantenha a lista vazia para proteger todas as rotas Web. Inclua apenas endpoints
     * que realmente precisem ser acessados sem cookie CSRF (ex.: webhooks externos
     * confiáveis). Avalie riscos antes de adicionar exceções.
     *
     * @var array<int, string>
     */
    protected $except = [];
}
