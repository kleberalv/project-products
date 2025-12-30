<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = [], string $sort = 'id', string $direction = 'desc'): LengthAwarePaginator;
    public function findById(int $id): ?Product;
    public function findByName(string $name): ?Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): Product;
    public function delete(int $id): bool;
    public function all(): \Illuminate\Database\Eloquent\Collection;
    public function getTrashed(int $perPage = 15): LengthAwarePaginator;
    public function restore(int $id): bool;
}
