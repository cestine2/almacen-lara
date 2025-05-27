<?php

namespace App\Contracts\Repositories;

use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Collection;

interface SucursalRepositoryInterface
{
    public function getAll(): Collection;
    public function getAllActive(): Collection;
    public function find(int $id): ?Sucursal;
    public function findActive(int $id): ?Sucursal;
    public function create(array $data): Sucursal;
    public function update(int $id, array $data): ?Sucursal;
    public function delete(int $id): bool; // "Eliminar" (cambiar estado a false)
    public function restore(int $id): bool; // Restaurar (cambiar estado a true)
}
