<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'sucursal' => SucursalResource::make($this->whenLoaded('sucursal')),
            'roles' => RolResource::make($this->whenLoaded('role')),
            'permissions' => $this->when($this->relationLoaded('role') || $this->hasAnyPermission(true), function () {
                return $this->getAllPermissions()->map(fn($permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ]);
            }),
            'estado' => $this->estado,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
