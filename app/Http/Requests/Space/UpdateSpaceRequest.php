<?php

namespace App\Http\Requests\Space;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'type' => 'sometimes|required|string|in:meeting_room,office,auditorium,laboratory,coworking_space,other',
            'capacity' => 'sometimes|required|integer|min:1',
            'price_per_hour' => 'sometimes|required|numeric|min:0',
            'location' => 'sometimes|required|string|max:255',
            'floor' => 'nullable|string|max:50',
            'amenities' => 'nullable|array',
            'image_url' => 'nullable|url',
            'is_available' => 'boolean',
            'rules' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del espacio es obligatorio',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'description.required' => 'La descripción es obligatoria',
            'type.required' => 'El tipo de espacio es obligatorio',
            'type.in' => 'El tipo de espacio no es válido',
            'capacity.required' => 'La capacidad es obligatoria',
            'capacity.integer' => 'La capacidad debe ser un número',
            'capacity.min' => 'La capacidad debe ser al menos 1',
            'price_per_hour.required' => 'El precio por hora es obligatorio',
            'price_per_hour.numeric' => 'El precio debe ser un número',
            'price_per_hour.min' => 'El precio no puede ser negativo',
            'location.required' => 'La ubicación es obligatoria',
            'location.max' => 'La ubicación no puede exceder 255 caracteres',
            'floor.max' => 'El piso no puede exceder 50 caracteres',
            'amenities.array' => 'Las amenidades deben ser un arreglo',
            'image_url.url' => 'La URL de la imagen debe ser válida',
            'is_available.boolean' => 'La disponibilidad debe ser verdadero o falso',
        ];
    }
}
