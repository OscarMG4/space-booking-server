<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'space_id' => 'sometimes|required|exists:spaces,id',
            'event_title' => 'sometimes|required|string|max:255',
            'event_description' => 'nullable|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
            'attendees_count' => 'nullable|integer|min:1',
            'special_requirements' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'space_id.required' => 'El espacio es obligatorio',
            'space_id.exists' => 'El espacio seleccionado no existe',
            'event_title.required' => 'El título del evento es obligatorio',
            'event_title.max' => 'El título no puede exceder 255 caracteres',
            'start_time.required' => 'La fecha de inicio es obligatoria',
            'start_time.date' => 'La fecha de inicio debe ser una fecha válida',
            'end_time.required' => 'La fecha de fin es obligatoria',
            'end_time.date' => 'La fecha de fin debe ser una fecha válida',
            'end_time.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'attendees_count.integer' => 'El número de asistentes debe ser un número',
            'attendees_count.min' => 'Debe haber al menos 1 asistente',
        ];
    }
}
