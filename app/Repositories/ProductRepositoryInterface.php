<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    /**
     * Lista produtos paginados com filtros e ordenacao.
     */
    public function paginate(int $perPage = 15, array $filters = [], string $sort = 'id', string $direction = 'desc'): LengthAwarePaginator;

    /**
     * Busca produto por ID.
     */
    public function findById(int $id): ?Product;

    /**
     * Busca produto por nome exato.
     */
    public function findByName(string $name): ?Product;

    /**
     * Cria um novo produto.
     */
    public function create(array $data): Product;

    /**
     * Atualiza dados de um produto existente.
     */
    public function update(int $id, array $data): Product;

    /**
     * Remove (soft delete) um produto.
     */
    public function delete(int $id): bool;

    /**
     * Retorna todos os produtos (sem paginar).
     */
    public function all(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Lista produtos deletados (soft delete) paginados.
     */
    public function getTrashed(int $perPage = 15): LengthAwarePaginator;

    /**
     * Restaura um produto deletado.
     */
    public function restore(int $id): bool;
}
