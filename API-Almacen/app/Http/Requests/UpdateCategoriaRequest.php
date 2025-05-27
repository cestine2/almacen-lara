<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('manage-product-categories');
    }

    public function rules(): array
    {
        $categoriaId = $this->route('id');

        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('categorias', 'nombre')->ignore($categoriaId),
            ],
            'descripcion' => ['nullable', 'string'],
            'tipo' => ['sometimes', 'required', Rule::in(['producto', 'material'])],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe otra categoría con este nombre.',
            'tipo.required' => 'El tipo de categoría es obligatorio (si se envía).',
            'tipo.in' => 'El tipo de categoría seleccionado no es válido.',
        ];
    }
}
