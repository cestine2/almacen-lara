<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProveedorRequest extends FormRequest
{
    public function authorize()
    {
        // Requiere login y permiso 'manage-suppliers'
        return auth()->check() && auth()->user()->can('manage-suppliers');
    }

    public function rules(): array
    {
        $proveedorId = $this->route('id');

        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('proveedores', 'nombre')->ignore($proveedorId),
            ],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

    // Opcional: mensajes personalizados
    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe otro proveedor con este nombre.',
        ];
    }
}
