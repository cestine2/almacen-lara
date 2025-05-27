<?php

namespace App\Contracts\Repositories;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

interface ProveedorRepositoryInterface
{
    public function getQuery(): Builder;
    public function getAll(): Collection;
    public function getAllActive(): Collection;
    public function find(int $id): ?Proveedor;
    public function findActive(int $id): ?Proveedor;
    public function create(array $data): Proveedor;
    public function update(int $id, array $data): ?Proveedor;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
}
