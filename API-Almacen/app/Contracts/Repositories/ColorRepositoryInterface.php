<?php

namespace App\Contracts\Repositories;

use App\Models\Color;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
interface ColorRepositoryInterface
{
    public function getQuery(): Builder;
    public function getAll(): Collection;
    public function getAllActive(): Collection;
    public function find(int $id): ?Color;
    public function findActive(int $id): ?Color;
    public function create(array $data): Color;
    public function update(int $id, array $data): ?Color;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
}
