<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class EloquentCategoriaRepository implements CategoriaRepositoryInterface
{
    public function getQuery(): Builder
    {
        return Categoria::query()->orderBy('id', 'desc');
    }

    public function getAll(): Collection
    {
        return Categoria::all();
    }

    public function getAllActive(): Collection
    {
        return Categoria::where('estado', true)->get();
    }

    public function find(int $id): ?Categoria
    {
        return Categoria::find($id);
    }

     public function findActive(int $id): ?Categoria
    {
        return Categoria::where('id', $id)->where('estado', true)->first();
    }

    public function create(array $data): Categoria
    {
        return Categoria::create($data);
    }

    public function update(int $id, array $data): ?Categoria
    {
        $categoria = $this->find($id);

        if (!$categoria) {
            return null;
        }

        $categoria->fill($data);
        $categoria->save();

        return $categoria;
    }

    public function delete(int $id): bool
    {
        $categoria = $this->findActive($id);

        if (!$categoria) {
            return false;
        }

        $categoria->estado = false;
        $categoria->save();

        return true;
    }

    public function restore(int $id): bool
    {
        $categoria = $this->find($id);

        if (!$categoria) {
            return false;
        }

        $categoria->estado = true;
        $categoria->save();

        return true;
    }
}
