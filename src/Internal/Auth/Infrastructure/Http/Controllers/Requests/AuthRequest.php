<?php
namespace Internal\Auth\Infrastructure\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class AuthRequest extends FormRequest
{
    public function validateRequest(Request $request): void
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'El email es requerido',
            'email.email' => 'El email no es válido',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser una cadena de texto',
        ]);
    }
}