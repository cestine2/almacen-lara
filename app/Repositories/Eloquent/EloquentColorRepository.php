<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\ColorRepositoryInterface;
use App\Models\Color;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class EloquentColorRepository implements ColorRepositoryInterface
{
    public function getQuery(): Builder
    {
        return Color::query()->orderBy('id', 'desc');
    }

    public function getAll(): Collection
    {
        return Color::orderBy('id', 'desc')->get();
    }

    public function getAllActive(): Collection
    {
        return Color::where('estado', true)->orderBy('id', 'desc')->get();
    }

    public function find(int $id): ?Color
    {
        return Color::find($id);
    }

     public function findActive(int $id): ?Color
    {
        return Color::where('id', $id)->where('estado', true)->first();
    }

    public function create(array $data): Color
    {
        return Color::create($data);
    }

    public function update(int $id, array $data): ?Color
    {
        $color = $this->find($id);

        if (!$color) {
            return null;
        }

        $color->fill($data);
        $color->save();

        return $color;
    }

    public function delete(int $id): bool
    {
        $color = $this->findActive($id);

        if (!$color) {
            return false;
        }

        $color->estado = false;
        $color->save();

        return true;
    }

    public function restore(int $id): bool
    {
        $color = $this->find($id);

        if (!$color) {
            return false;
        }

        $color->estado = true;
        $color->save();

        return true;
    }
}
