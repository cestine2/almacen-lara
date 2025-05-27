<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;


class EloquentMaterialRepository implements MaterialRepositoryInterface
{
    /**
     * Obtiene el Query Builder base para el modelo Material.
     * Útil para aplicar filtros y paginación en el Servicio.
     */
    public function getQuery(): Builder
    {
        return Material::query()->orderBy('id', 'desc');
    }

    public function getAll(): Collection
    {
        return Material::all();
    }

    public function getAllActive(): Collection
    {
        return Material::where('estado', true)->get();
    }

    public function find(int $id): ?Material
    {
        return Material::find($id);
    }

     public function findActive(int $id): ?Material
    {
        return Material::where('id', $id)->where('estado', true)->first();
    }

    public function create(array $data): Material
    {
        return Material::create($data);
    }

    public function update(int $id, array $data): ?Material
    {
        $material = $this->find($id);

        if (!$material) {
            return null;
        }

        $material->fill($data);
        $material->save();

        return $material;
    }

    public function delete(int $id): bool
    {
        $material = $this->findActive($id);

        if (!$material) {
            return false;
        }

        $material->estado = false;
        $material->save();

        return true;
    }

    public function restore(int $id): bool
    {
        $material = $this->find($id);

        if (!$material) {
            return false;
        }

        $material->estado = true;
        $material->save();

        return true;
    }
}
