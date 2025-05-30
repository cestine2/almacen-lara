<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('manage-users');
    }
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'string', 'min:8'],
            'sucursal_id' => ['required', 'integer', 'exists:sucursales,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'estado' => ['boolean'],
        ];
    }
    public function messages(): array
    {
        return [
            'email.unique' => 'El correo electrónico ya está registrado.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
            'role_id.exists' => 'El rol seleccionado no existe.',
        ];
    }
}
