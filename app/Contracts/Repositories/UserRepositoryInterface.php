<?php
namespace App\Contracts\Repositories;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

interface UserRepositoryInterface
{
    public function getQuery(): Builder;
    public function getAllActive(): Collection;
    public function find(int $id): ?User;
    public function findActive(int $id): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): ?User;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
}
