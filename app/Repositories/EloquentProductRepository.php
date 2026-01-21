<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepositoryInterface
{
    private Product $model;

    /**
     * Injeta o model Product.
     */
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * Lista produtos paginados com filtros e ordenacao.
     */
    public function paginate(int $perPage = 15, array $filters = [], string $sort = 'id', string $direction = 'desc'): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('nome', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
        }

        if (isset($filters['preco_min'])) {
            $query->where('preco', '>=', $filters['preco_min']);
        }

        if (isset($filters['preco_max'])) {
            $query->where('preco', '<=', $filters['preco_max']);
        }

        if (isset($filters['estoque_min'])) {
            $query->where('quantidade_estoque', '>=', $filters['estoque_min']);
        }

        if (isset($filters['estoque_max'])) {
            $query->where('quantidade_estoque', '<=', $filters['estoque_max']);
        }

        return $query->orderBy($sort, $direction)->paginate($perPage);
    }

    /**
     * Busca produto por ID.
     */
    public function findById(int $id): ?Product
    {
        return $this->model->find($id);
    }

    /**
     * Busca produto por nome exato.
     */
    public function findByName(string $name): ?Product
    {
        return $this->model->where('nome', $name)->first();
    }

    /**
     * Cria um novo produto.
     */
    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    /**
     * Atualiza um produto existente.
     */
    public function update(int $id, array $data): Product
    {
        $product = $this->findById($id);
        $product->update($data);
        return $product;
    }

    /**
     * Executa soft delete em um produto.
     */
    public function delete(int $id): bool
    {
        $product = $this->findById($id);
        return $product ? (bool) $product->delete() : false;
    }

    /**
     * Retorna todos os produtos (sem paginar).
     */
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }

    /**
     * Lista produtos deletados (soft delete) paginados.
     */
    public function getTrashed(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->onlyTrashed()->paginate($perPage);
    }

    /**
     * Restaura um produto deletado.
     */
    public function restore(int $id): bool
    {
        $product = $this->model->onlyTrashed()->find($id);
        return $product ? (bool) $product->restore() : false;
    }
}
