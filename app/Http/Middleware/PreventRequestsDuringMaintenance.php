<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * Endpoints liberados durante o modo de manutenção.
     *
     * Rotas listadas aqui continuarão acessíveis mesmo com `php artisan down`. Útil para
     * health-checks de load balancer ou webhooks críticos. Mantenha vazio para bloquear
     * tudo.
     *
     * @var array<int, string>
     */
    protected $except = [
    ];
}
