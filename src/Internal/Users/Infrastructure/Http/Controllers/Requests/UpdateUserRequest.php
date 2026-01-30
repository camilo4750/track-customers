<?php

namespace Internal\Users\Infrastructure\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function validateRequest(Request $request, int $id): void
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'required|string|in:user,admin',
            'status' => 'required|string|in:active,inactive',
        ], [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'email.required' => 'El email es requerido',
            'email.string' => 'El email debe ser una cadena de texto',
            'email.email' => 'El email no es válido',
            'email.unique' => 'El email ya está registrado',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser una cadena de texto',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'role.required' => 'El rol es requerido',
            'role.string' => 'El rol debe ser una cadena de texto',
            'status.required' => 'El estado es requerido',
        ]);
    }
}
