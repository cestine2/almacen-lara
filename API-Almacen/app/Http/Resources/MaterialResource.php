<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoriaResource;
use App\Http\Resources\ProveedorResource;
use App\Http\Resources\SucursalResource;
use App\Http\Resources\ColorResource;


class MaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cod_articulo' => $this->cod_articulo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria_id' => $this->categoria_id,
            'proveedor_id' => $this->proveedor_id,
            'codigo_barras' => $this->codigo_barras,
            'color_id' => $this->color_id,
            'estado' => $this->estado,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'categoria' => CategoriaResource::make($this->whenLoaded('categoria')),
            'proveedor' => ProveedorResource::make($this->whenLoaded('proveedor')),
            'sucursal' => SucursalResource::make($this->whenLoaded('sucursal')),
            'color' => ColorResource::make($this->whenLoaded('color')),
        ];
    }
}
