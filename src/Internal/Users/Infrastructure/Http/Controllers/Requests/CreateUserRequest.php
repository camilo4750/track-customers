<?php

namespace Internal\Users\Infrastructure\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateUserRequest extends FormRequest
{
    public function validateRequest(Request $request): void
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|min:8|same:password',
            'role' => 'required|string|in:user,admin',
            'status' => 'required|string|in:active,inactive',
        ], [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email no es válido',
            'email.unique' => 'El email ya está registrado',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser una cadena de texto',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password_confirmation.required' => 'La confirmación de la contraseña es requerida',
            'password_confirmation.same' => 'La confirmación de la contraseña no coincide',
            'role.required' => 'El rol es requerido',
            'status.required' => 'El estado es requerido',
        ]);
    }
}
