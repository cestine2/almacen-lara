<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;


class EloquentProductRepository implements ProductRepositoryInterface
{
    /**
     * Obtiene el Query Builder base para el modelo Product.
     * Útil para aplicar filtros y paginación en el Servicio.
     */
    public function getQuery(): Builder
    {
        return Product::query()->orderBy('id', 'desc');
    }

    public function getAll(): Collection
    {
        return Product::all();
    }

    public function getAllActive(): Collection
    {
        return Product::where('estado', true)->get();
    }

    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findActive(int $id): ?Product
    {
        return Product::where('id', $id)->where('estado', true)->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): ?Product
    {
        $product = $this->find($id);

        if (!$product) {
            return null;
        }

        $product->fill($data);
        $product->save();

        return $product;
    }

    public function delete(int $id): bool
    {
        $product = $this->findActive($id);

        if (!$product) {
            return false;
        }

        $product->estado = false;
        $product->save();

        return true;
    }

    public function restore(int $id): bool
    {
        $product = $this->find($id);

        if (!$product) {
            return false;
        }

        $product->estado = true;
        $product->save();

        return true;
    }
}
