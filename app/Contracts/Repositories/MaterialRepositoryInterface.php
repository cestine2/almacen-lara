<?php

namespace App\Contracts\Repositories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder; // Para métodos que devuelven el Query Builder

interface MaterialRepositoryInterface
{
    public function getQuery(): Builder;
    public function getAll(): Collection;
    public function getAllActive(): Collection;
    public function find(int $id): ?Material;
    public function findActive(int $id): ?Material;
    public function create(array $data): Material;
    public function update(int $id, array $data): ?Material;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
}
