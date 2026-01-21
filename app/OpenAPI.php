<?php

namespace App;

/**
 * OpenAPI Global Configuration
 * 
 * @OA\Info(
 *     version="1.0.0",
 *     title="Gerenciador de Produtos - API",
 *     description="API REST completa para gerenciamento de produtos, autenticação e usuários. Implementada em Laravel 10 com Sanctum para autenticação stateless.",
 *     contact=@OA\Contact(
 *         email="admin@gerenciador-produtos.com"
 *     ),
 *     license=@OA\License(
 *         name="MIT License"
 *     )
 * )
 *
 * @OA\Server(url="/api", description="API Server")
 *
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="token",
 *         securityScheme="bearerAuth",
 *         description="Token de autenticação Bearer (obtido no login)"
 *     )
 * )
 */
class OpenAPI
{
    // Arquivo dedicado para anotações OpenAPI globais
    // Este arquivo é escaneado pelo L5-Swagger para gerar a documentação
}
