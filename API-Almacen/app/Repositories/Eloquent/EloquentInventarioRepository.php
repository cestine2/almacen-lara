<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\InventarioRepositoryInterface;
use App\Models\Inventario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;


class EloquentInventarioRepository implements InventarioRepositoryInterface
{
    /**
     * Obtiene el Query Builder base para el modelo Inventario.
     * Útil para aplicar filtros y paginación en el Servicio.
     */
    public function getQuery(): Builder
    {
        return Inventario::query()->orderBy('id', 'desc');
    }

    public function getAll(): Collection
    {
        return Inventario::all();
    }

    public function getAllActive(): Collection
    {
        return Inventario::where('estado', true)->get();
    }

    public function find(int $id): ?Inventario
    {
        return Inventario::find($id);
    }

     public function findActive(int $id): ?Inventario
    {
        return Inventario::where('id', $id)->where('estado', true)->first();
    }

    public function create(array $data): Inventario
    {
        return Inventario::create($data);
    }

    public function update(int $id, array $data): ?Inventario
    {
        $inventario = $this->find($id);

        if (!$inventario) {
            return null;
        }

        unset($data['tipo'], $data['material_id'], $data['producto_id']);

        $inventario->fill($data);
        $inventario->save();

        return $inventario;
    }

    public function delete(int $id): bool
    {
        $inventario = $this->findActive($id);

        if (!$inventario) {
            return false;
        }

        $inventario->estado = false;
        $inventario->save();

        return true;
    }

    public function restore(int $id): bool
    {
        $inventario = $this->find($id);

        if (!$inventario) {
            return false;
        }

        $inventario->estado = true;
        $inventario->save();

        return true;
    }

    public function findByItemAndSucursal(string $tipo, int $itemId, int $sucursalId): ?Inventario
    {
        $query = $this->getQuery()->where('tipo', $tipo)->where('sucursal_id', $sucursalId);

        if ($tipo === 'Material') {
            $query->where('material_id', $itemId)->whereNull('producto_id');
        } elseif ($tipo === 'Producto') {
            $query->where('producto_id', $itemId)->whereNull('material_id');
        } else {
            return null;
        }

        return $query->first();
    }
}
