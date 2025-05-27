<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- Importa Rule

class StoreInventarioRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('register-inventory');
    }

    public function rules(): array
    {
        $rules = [
            'tipo' => ['required', Rule::in(['Material', 'Producto'])],
            'stock_actual' => ['required', 'integer', 'min:0'],
            'estado' => ['boolean'],
        ];

        if ($this->input('tipo') === 'Material') {
            $rules['material_id'] = ['required', 'integer', 'exists:materiales,id'];
            $rules['producto_id'] = ['nullable', 'integer'];
            $rules['sucursal_id'] = ['nullable', 'integer'];
        } elseif ($this->input('tipo') === 'Producto') {
            $rules['producto_id'] = ['required', 'integer', 'exists:productos,id'];
            $rules['material_id'] = ['nullable', 'integer'];
            $rules['sucursal_id'] = ['required', 'integer', 'exists:sucursales,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'El tipo de inventario (Material o Producto) es obligatorio.',
            'tipo.in' => 'El tipo de inventario debe ser Material o Producto.',
            'stock_actual.required' => 'El stock actual es obligatorio.',
            'sucursal_id.required' => 'La sucursal es obligatoria.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
            'material_id.required' => 'El ID del material es obligatorio cuando el tipo es Material.',
            'material_id.exists' => 'El material seleccionado no existe.',
            'producto_id.required' => 'El ID del producto es obligatorio cuando el tipo es Producto.',
            'producto_id.exists' => 'El producto seleccionado no existe.',
        ];
    }
}
