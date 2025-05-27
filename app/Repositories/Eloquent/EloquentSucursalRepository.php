<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\SucursalRepositoryInterface;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Collection;

class EloquentSucursalRepository implements SucursalRepositoryInterface
{
    public function getAll(): Collection
    {
        return Sucursal::orderBy('id', 'desc')->get();
    }

    public function getAllActive(): Collection
    {
        return Sucursal::where('estado', true)->orderBy('id', 'desc')->get();
    }

    public function find(int $id): ?Sucursal
    {
        return Sucursal::find($id);
    }

     public function findActive(int $id): ?Sucursal
    {
        return Sucursal::where('id', $id)->where('estado', true)->first();
    }

    public function create(array $data): Sucursal
    {
        return Sucursal::create($data);
    }

    public function update(int $id, array $data): ?Sucursal
    {
        $sucursal = $this->find($id);

        if (!$sucursal) {
            return null;
        }

        $sucursal->fill($data);
        $sucursal->save();

        return $sucursal;
    }

    // Implementación del "soft delete" cambiando el estado
    public function delete(int $id): bool
    {
        $sucursal = $this->findActive($id);

        if (!$sucursal) {
            return false;
        }

        $sucursal->estado = false;
        $sucursal->save();

        return true;
    }

    // Implementación para restaurar (cambiar estado a true)
    public function restore(int $id): bool
    {
        $sucursal = $this->find($id);

        if (!$sucursal) {
            return false;
        }

        $sucursal->estado = true;
        $sucursal->save();

        return true;
    }
}
