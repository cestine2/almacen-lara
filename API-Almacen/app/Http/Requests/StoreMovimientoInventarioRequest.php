<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMovimientoInventarioRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('register-inventory-movement');
    }

    public function rules(): array
    {
        $rules = [
            'motivo' => ['required', Rule::in(['entrada', 'salida', 'ajuste'])],
            'descripcion' => ['nullable', 'string'],
            'tipo' => ['required', Rule::in(['Material', 'Producto'])],
            'cantidad' => ['required', 'integer', 'min:1'],
            'precio_unitario' => ['numeric', 'min:0'],
            'sucursal_id' => ['required', 'integer', 'exists:sucursales,id'],
        ];
        if ($this->input('tipo') === 'Material') {
            $rules['material_id'] = ['required', 'integer', 'exists:materiales,id'];
            $rules['producto_id'] = ['nullable', 'integer'];
            $rules['precio_unitario'][] = 'nullable';
        } elseif ($this->input('tipo') === 'Producto') {
            $rules['producto_id'] = ['required', 'integer', 'exists:productos,id'];
            $rules['material_id'] = ['nullable', 'integer'];
            $rules['precio_unitario'][] = 'required';
        }

        $motivo = $this->input('motivo');
        if ($motivo === 'entrada' || $motivo === 'salida') {
            $rules['cantidad'][] = 'min:1';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'motivo.required' => 'El motivo del movimiento es obligatorio.',
            'motivo.in' => 'El motivo debe ser entrada, salida o ajuste.',
            'tipo.required' => 'El tipo de ítem (Material o Producto) es obligatorio.',
            'tipo.in' => 'El tipo de ítem debe ser Material o Producto.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser al menos 1 para entradas y salidas.',
            'precio_unitario.required' => 'El precio unitario es obligatorio para movimientos de Producto.',
            'precio_unitario.numeric' => 'El precio unitario debe ser un número.',
            'precio_unitario.min' => 'El precio unitario no puede ser negativo.',
            'sucursal_id.required' => 'La sucursal es obligatoria.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
            'material_id.required' => 'El ID del material es obligatorio cuando el tipo es Material.',
            'material_id.exists' => 'El material seleccionado no existe.',
            'producto_id.required' => 'El ID del producto es obligatorio cuando el tipo es Producto.',
            'producto_id.exists' => 'El producto seleccionado no existe.',
        ];
    }
}
