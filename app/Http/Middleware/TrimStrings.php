<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * Campos que não devem ser aparados (trim) automaticamente.
     *
     * Útil para senhas e confirmações, onde espaços são significativos ou devem ser
     * preservados. Mantenha aqui qualquer campo sensível a whitespace.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
