<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('manage-products');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:productos,nombre'],
            'descripcion' => ['nullable', 'string'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'talla' => ['required', 'string', 'max:10'],
            'color_id' => ['required', 'integer', 'exists:colores,id'],
            'precio' => ['required', 'numeric', 'min:0'],
            'codigo_barras' => ['nullable', 'string', 'max:100', 'unique:productos,codigo_barras'],
            'estado' => ['boolean'],
        ];
    }

    // Opcional: mensajes personalizados
    public function messages(): array
    {
         return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.unique' => 'Ya existe un producto con este nombre.',
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'color_id.exists' => 'El color seleccionado no existe.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
            'codigo_barras.unique' => 'Este código de barras ya está registrado para otro producto.',
            'talla.required' => 'La tabla es obligatoria'
         ];
    }
}
