<?php

namespace Internal\Seeders\Infrastructure\Http\Controllers\Requests;

use Illuminate\Http\Request;

class RunSeederRequest
{
    public function validateRequest(Request $request): array
    {
        $validKeys = array_keys(config('seeders.seeders', []));

        return $request->validate([
            'key' => ['required', 'string', 'in:'.implode(',', $validKeys)],
            'count' => ['sometimes', 'integer', 'min:1', 'max:10000'],
        ], [
            'key.required' => 'El identificador del seeder es requerido.',
            'key.in' => 'El seeder indicado no existe.',
            'count.integer' => 'La cantidad debe ser un número entero.',
            'count.min' => 'La cantidad debe ser al menos 1.',
            'count.max' => 'La cantidad no puede superar 10000.',
        ]);
    }
}
