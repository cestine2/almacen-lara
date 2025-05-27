<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SucursalResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\ProductResource;


class MovimientoInventarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'motivo' => $this->motivo,
            'tipo' => $this->tipo,
            'material_id' => $this->material_id,
            'producto_id' => $this->producto_id,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->precio_unitario,
            'total' => $this->total,
            'sucursal_id' => $this->sucursal_id,
            'usuario_id' => $this->usuario_id,
            'created_at' => $this->created_at,
            'sucursal' => SucursalResource::make($this->whenLoaded('sucursal')),
            'usuario' => UserResource::make($this->whenLoaded('usuario')),
            'item_asociado' => match ($this->tipo) {
                'Material' => $this->whenLoaded('material', function () {
                     return MaterialResource::make($this->material);
                }),
                'Producto' => $this->whenLoaded('producto', function () {
                     return ProductResource::make($this->producto);
                }),
                default => null,
            },
            'material' => MaterialResource::make($this->whenLoaded('material')),
            'producto' => ProductResource::make($this->whenLoaded('producto')),
        ];
    }
}
