<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
// Importa los Resources de las relaciones
use App\Http\Resources\SucursalResource;
use App\Http\Resources\UserResource; // Asume que tienes un UserResource
use App\Http\Resources\MaterialResource; // Importa MaterialResource
use App\Http\Resources\ProductResource; // Importa ProductResource


class InventarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'material_id' => $this->material_id,
            'producto_id' => $this->producto_id,
            'stock_actual' => $this->stock_actual,
            'sucursal_id' => $this->sucursal_id,
            'usuario_id' => $this->usuario_id,
            // 'estado' => $this->estado,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sucursal' => SucursalResource::make($this->whenLoaded('sucursal')),
            'usuario' => UserResource::make($this->whenLoaded('usuario')),
            'item' => match ($this->tipo) {
                'Material' => $this->whenLoaded('material', function () {
                    return MaterialResource::make($this->material);
                }),
                'Producto' => $this->whenLoaded('producto', function () {
                    return ProductResource::make($this->producto);
                }),
                default => null,
            },
        ];
    }
}
