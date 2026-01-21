<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * Lista de cookies que não devem ser criptografados pelo Laravel.
     *
     * Mantenha vazia para aplicar criptografia a todos os cookies padrão. Adicione
     * nomes aqui apenas se algum provedor externo exigir leitura em texto claro.
     *
     * @var array<int, string>
     */
    protected $except = [
    ];
}
