<?php

namespace App\Contracts\Repositories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

interface CategoriaRepositoryInterface
{
    public function getQuery(): Builder;
    public function getAll(): Collection;
    public function getAllActive(): Collection;
    public function find(int $id): ?Categoria;
    public function findActive(int $id): ?Categoria;
    public function create(array $data): Categoria;
    public function update(int $id, array $data): ?Categoria;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
}
