<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepositoryInterface
{
    private Product $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

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

    public function findById(int $id): ?Product
    {
        return $this->model->find($id);
    }

    public function findByName(string $name): ?Product
    {
        return $this->model->where('nome', $name)->first();
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->findById($id);
        $product->update($data);
        return $product;
    }

    public function delete(int $id): bool
    {
        $product = $this->findById($id);
        return $product ? (bool) $product->delete() : false;
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->all();
    }

    public function getTrashed(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->onlyTrashed()->paginate($perPage);
    }

    public function restore(int $id): bool
    {
        $product = $this->model->onlyTrashed()->find($id);
        return $product ? (bool) $product->restore() : false;
    }
}
