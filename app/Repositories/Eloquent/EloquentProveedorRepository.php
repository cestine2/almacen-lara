<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\ProveedorRepositoryInterface;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class EloquentProveedorRepository implements ProveedorRepositoryInterface
{

    public function getQuery(): Builder
    {
        return Proveedor::query()->orderBy('id', 'desc');
    }

    public function getAll(): Collection
    {
        return Proveedor::all();
    }

    public function getAllActive(): Collection
    {
        return Proveedor::where('estado', true)->get();
    }

    public function find(int $id): ?Proveedor
    {
        return Proveedor::find($id);
    }

     public function findActive(int $id): ?Proveedor
    {
        return Proveedor::where('id', $id)->where('estado', true)->first();
    }

    public function create(array $data): Proveedor
    {
        return Proveedor::create($data);
    }

    public function update(int $id, array $data): ?Proveedor
    {
        $proveedor = $this->find($id);

        if (!$proveedor) {
            return null;
        }

        $proveedor->fill($data);
        $proveedor->save();

        return $proveedor;
    }

    public function delete(int $id): bool
    {
        $proveedor = $this->findActive($id);

        if (!$proveedor) {
            return false;
        }

        $proveedor->estado = false;
        $proveedor->save();

        return true;
    }

    public function restore(int $id): bool
    {
        $proveedor = $this->find($id);

        if (!$proveedor) {
            return false;
        }

        $proveedor->estado = true;
        $proveedor->save();

        return true;
    }
}
