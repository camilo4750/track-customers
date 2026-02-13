<?php

namespace Internal\Products\Infrastructure\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateProductRequest extends FormRequest
{
    public function validateRequest(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
        ], [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'sku.required' => 'El SKU es requerido',
            'sku.string' => 'El SKU debe ser una cadena de texto',
            'sku.max' => 'El SKU debe tener menos de 100 caracteres',
            'sku.unique' => 'El SKU ya está registrado',
            'price.required' => 'El precio es requerido',
            'price.numeric' => 'El precio debe ser un número',
            'price.min' => 'El precio no puede ser negativo',
            'category.string' => 'La categoría debe ser una cadena de texto',
            'category.max' => 'La categoría debe tener menos de 100 caracteres',
        ]);
    }
}
