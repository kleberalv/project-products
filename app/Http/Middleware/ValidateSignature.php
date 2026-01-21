<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ValidateSignature as Middleware;

class ValidateSignature extends Middleware
{
    /**
     * Query params ignorados na validação da assinatura de URL.
     *
     * Útil para parâmetros de rastreamento (utm, fbclid) que não devem invalidar links
     * assinados. Adicione aqui quaisquer chaves de query que devam ser desconsideradas.
     *
     * @var array<int, string>
     */
    protected $except = [
        // 'fbclid',
        // 'utm_campaign',
        // 'utm_content',
        // 'utm_medium',
        // 'utm_source',
        // 'utm_term',
    ];
}
