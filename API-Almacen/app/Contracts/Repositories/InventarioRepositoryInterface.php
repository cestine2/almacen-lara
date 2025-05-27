<?php

namespace App\Contracts\Repositories;

use App\Models\Inventario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

interface InventarioRepositoryInterface
{
    public function getQuery(): Builder;
    public function getAll(): Collection;
    public function getAllActive(): Collection;
    public function find(int $id): ?Inventario;
    public function findActive(int $id): ?Inventario;
    public function create(array $data): Inventario;
    public function update(int $id, array $data): ?Inventario;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function findByItemAndSucursal(string $tipo, int $itemId, int $sucursalId): ?Inventario;
}
