<?php

namespace Internal\Clients\Infrastructure\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function validateRequest(Request $request, int $id): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email,' . $id,
            'phone' => 'required|string|max:50',
            'status' => 'required|string|in:active,inactive',
            'tags' => 'required|array',
            'tags.*' => 'string|max:50',
        ], [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email no es válido',
            'email.max' => 'El email debe tener menos de 255 caracteres',
            'email.unique' => 'El email ya está registrado',
            'phone.required' => 'El teléfono es requerido',
            'phone.string' => 'El teléfono debe ser una cadena de texto',
            'phone.max' => 'El teléfono debe tener menos de 50 caracteres',
            'status.required' => 'El estado es requerido',
            'status.string' => 'El estado debe ser una cadena de texto',
            'status.in' => 'El estado debe ser active o inactive, no se permiten otros valores',
            'tags.required' => 'Los tags son requeridos',
            'tags.array' => 'Los tags deben ser un array',
            'tags.*.string' => 'Cada tag debe ser una cadena de texto',
            'tags.*.max' => 'Cada tag debe tener menos de 50 caracteres',
        ]);
    }
}

