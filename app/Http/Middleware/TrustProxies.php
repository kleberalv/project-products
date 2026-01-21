<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Lista de proxies confiáveis para detecção correta do IP/host originais.
     *
     * Mantenha null para confiar nos proxies padrão do ambiente ou defina IPs/CIDRs
     * específicos quando estiver atrás de load balancers conhecidos.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * Conjunto de headers usados para identificar informações de proxy.
     *
     * Inclui X-Forwarded-* e AWS ELB. Ajuste se o provedor usar cabeçalhos diferentes.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
