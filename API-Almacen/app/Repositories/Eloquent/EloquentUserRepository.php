<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function getQuery(): Builder
    {
        return User::query();
    }

    public function getAllActive(): Collection
    {
        return User::where('estado', true)->get();
    }

    public function find(int $id): ?User
    {
        return User::with(['sucursal', 'role'])->find($id);
    }

    public function findActive(int $id): ?User
    {
        return User::where('id', $id)->where('estado', true)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): ?User
    {
        $user = $this->find($id);
        if (!$user) {
            return null;
        }

        $user->fill($data);
        $user->save();

        return $user;
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
        $user = $this->find($id);

        if (!$user) {
            return false;
        }

        $user->estado = true;
        $user->save();

        return true;
    }
}
