<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('manage-product-categories');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:categorias,nombre'],
            'descripcion' => ['nullable', 'string'],
            'tipo' => ['required', Rule::in(['producto', 'material'])],
            'estado' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con este nombre.',
            'tipo.required' => 'El tipo de categoría es obligatorio.',
            'tipo.in' => 'El tipo de categoría seleccionado no es válido.',
        ];
    }
}
