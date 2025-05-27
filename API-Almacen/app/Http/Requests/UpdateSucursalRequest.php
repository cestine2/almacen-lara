<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSucursalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage-branches');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $sucursalId = $this->route('id');

        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('sucursales', 'nombre')->ignore($sucursalId),
            ],
            'direccion' => ['nullable', 'string', 'max:255'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe otra sucursal con este nombre.',
        ];
    }
}
