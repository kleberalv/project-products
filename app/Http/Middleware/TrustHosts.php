<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Define os padrões de host confiáveis para validação de headers.
     *
     * Retorna todos os subdomínios da URL da aplicação configurada, evitando
     * ataques de host header spoofing. Adicione padrões extras aqui se precisar
     * confiar em domínios adicionais (ex.: ambientes de preview).
     *
     * @return array<int, string|null> Lista de padrões de host aceitos.
     */
    public function hosts(): array
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}
