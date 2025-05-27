<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\MovimientoInventarioRepositoryInterface;
use App\Models\MovimientoInventario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator; // Puede no usarse aquí
use Illuminate\Database\Eloquent\Builder;


class EloquentMovimientoInventarioRepository implements MovimientoInventarioRepositoryInterface
{
    /**
     * Obtiene el Query Builder base para el modelo MovimientoInventario, con ordenación por defecto (id DESC).
     *
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return MovimientoInventario::query()->orderBy('id', 'desc');
    }

    public function getAll(): Collection
    {
        return $this->getQuery()->get();
    }

    public function find(int $id): ?MovimientoInventario
    {
        return MovimientoInventario::find($id);
    }

    public function create(array $data): MovimientoInventario
    {
        $data['created_at'] = $data['created_at'] ?? now();
        return MovimientoInventario::create($data);
    }
}
