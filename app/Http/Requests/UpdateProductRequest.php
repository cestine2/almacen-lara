<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- Importa Rule

class UpdateProductRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('manage-products');
    }

    public function rules(): array
    {
        $productId = $this->route('id');

        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('productos', 'nombre')->ignore($productId),
            ],
            'descripcion' => ['nullable', 'string'],
            'categoria_id' => ['sometimes', 'required', 'integer', 'exists:categorias,id'],
            'talla' => ['nullable', 'string', 'max:10'],
            'color_id' => ['nullable', 'integer', 'exists:colores,id'],
            'precio' => ['sometimes', 'required', 'numeric', 'min:0'],
            'codigo_barras' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('productos', 'codigo_barras')->ignore($productId),
            ],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe otro producto con este nombre.',
            'categoria_id.required' => 'La categoría es obligatoria (si se envía).',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'color_id.exists' => 'El color seleccionado no existe.',
            'precio.required' => 'El precio es obligatorio (si se envía).',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
            'codigo_barras.unique' => 'Este código de barras ya está registrado para otro producto.',
            'talla.required' => 'La tabla es obligatoria'
        ];
    }
}
