<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'La reserva es obligatoria',
            'booking_id.exists' => 'La reserva no existe',
            'rating.required' => 'La calificación es obligatoria',
            'rating.integer' => 'La calificación debe ser un número',
            'rating.min' => 'La calificación mínima es 1',
            'rating.max' => 'La calificación máxima es 5',
            'comment.required' => 'El comentario es obligatorio',
            'comment.max' => 'El comentario no puede exceder 1000 caracteres',
        ];
    }
}
