<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaterialRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('manage-materials');
    }

    public function rules(): array
    {
        return [
            'cod_articulo' => ['required', 'string', 'max:255', 'unique:materiales,cod_articulo'],
            'nombre' => ['required', 'string', 'max:255', 'unique:materiales,nombre'],
            'descripcion' => ['nullable', 'string'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'proveedor_id' => ['required', 'integer', 'exists:proveedores,id'],
            'codigo_barras' => ['nullable', 'string', 'max:100', 'unique:materiales,codigo_barras'],
            'color_id' => ['required', 'integer', 'exists:colores,id'],
            'estado' => ['boolean'],
        ];
    }

    // Opcional: mensajes personalizados
    public function messages(): array
    {
        return [
            'cod_articulo.unique' => 'Este código de artículo ya está registrado para otro material.',
            'nombre.required' => 'El nombre del material es obligatorio.',
            'nombre.unique' => 'Ya existe un material con este nombre.',
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'proveedor_id.required' => 'El proveedor es obligatorio.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
            'codigo_barras.unique' => 'Este código de barras ya está registrado para otro material.',
            'color_id.exists' => 'El color seleccionado no existe.',
        ];
    }
}
