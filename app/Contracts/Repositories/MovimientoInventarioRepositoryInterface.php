<?php

namespace App\Contracts\Repositories;

use App\Models\MovimientoInventario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

interface MovimientoInventarioRepositoryInterface
{
    public function getQuery(): Builder;
    public function getAll(): Collection;
    public function find(int $id): ?MovimientoInventario;
    public function create(array $data): MovimientoInventario;
}
