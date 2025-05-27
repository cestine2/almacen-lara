<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaterialRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('manage-materials');
    }

    public function rules(): array
    {
        $materialId = $this->route('id');
        return [
            'cod_articulo' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('materiales', 'cod_articulo')->ignore($materialId),
            ],
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('materiales', 'nombre')->ignore($materialId),
            ],
            'descripcion' => ['nullable', 'string'],
            'categoria_id' => ['sometimes', 'required', 'integer', 'exists:categorias,id'],
            'proveedor_id' => ['sometimes', 'required', 'integer', 'exists:proveedores,id'],
            'codigo_barras' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('materiales', 'codigo_barras')->ignore($materialId),
            ],
            'color_id' => ['nullable', 'integer', 'exists:colores,id'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

     // Opcional: mensajes personalizados
     public function messages(): array
     {
        return [
            'cod_articulo.unique' => 'Este código de artículo ya está registrado para otro material.',
            'nombre.unique' => 'Ya existe otro material con este nombre.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
            'codigo_barras.unique' => 'Este código de barras ya está registrado para otro material.',
            'color_id.exists' => 'El color seleccionado no existe.',
        ];
     }
}
