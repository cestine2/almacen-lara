<?php

namespace App\Contracts\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

interface ProductRepositoryInterface
{
    public function getQuery(): Builder;
    public function getAll(): Collection;
    public function getAllActive(): Collection;
    public function find(int $id): ?Product;
    public function findActive(int $id): ?Product;
    public function create(array $data): Product;
    public function update(int $id, array $data): ?Product;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
}
