<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateColorRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('manage-colors');
    }

    public function rules(): array
    {
        $colorId = $this->route('id');
        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('colores', 'nombre')->ignore($colorId),
            ],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe otro color con este nombre.',
        ];
    }
}
