<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize()
    {
        // Requiere login y permiso 'manage-suppliers'
        return auth()->check() && auth()->user()->can('manage-suppliers');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:proveedores,nombre'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'estado' => ['boolean'],
        ];
    }

    // Opcional: mensajes personalizados
    public function messages(): array
    {
         return [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'nombre.unique' => 'Ya existe un proveedor con este nombre.',
         ];
    }
}
