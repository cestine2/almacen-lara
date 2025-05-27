<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoriaResource;
use App\Http\Resources\ColorResource;
use App\Http\Resources\SucursalResource;


class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria_id' => $this->categoria_id,
            'talla' => $this->talla,
            'color_id' => $this->color_id,
            'precio' => $this->precio,
            'codigo_barras' => $this->codigo_barras,
            'estado' => $this->estado,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'categoria' => CategoriaResource::make($this->whenLoaded('categoria')),
            'color' => ColorResource::make($this->whenLoaded('color')),
            'sucursal' => SucursalResource::make($this->whenLoaded('sucursal')),
        ];
    }
}
