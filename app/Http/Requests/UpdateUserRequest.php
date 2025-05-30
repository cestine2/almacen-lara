<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage-users');
    }
    public function rules(): array
    {
        $userId = $this->route('id');
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'sucursal_id' => ['sometimes', 'required', 'integer', 'exists:sucursales,id'],
            'role_id' => ['sometimes', 'integer', 'required', 'exists:roles,id'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }
    public function messages(): array
    {
        return [
            'email.unique' => 'El correo electrónico ya está registrado por otro usuario.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
            'role_id.exists' => 'El rol seleccionado no existe.',
        ];
    }
}
