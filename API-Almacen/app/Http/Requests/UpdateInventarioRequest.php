<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventarioRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('register-inventory');
    }

    public function rules(): array
    {
        $inventarioId = $this->route('id');

        return [
            'stock_actual' => ['sometimes', 'required', 'integer', 'min:0'],
            'sucursal_id' => ['sometimes', 'required', 'integer', 'exists:sucursales,id'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'stock_actual.required' => 'El stock actual es obligatorio.',
            'stock_actual.integer' => 'El stock actual debe ser un número entero.',
            'stock_actual.min' => 'El stock actual no puede ser negativo.',
            'sucursal_id.required' => 'La sucursal es obligatoria.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
        ];
    }
}
