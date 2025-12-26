<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_title' => $this->event_title,
            'event_description' => $this->event_description,
            'start_time' => $this->start_time?->toISOString(),
            'end_time' => $this->end_time?->toISOString(),
            'status' => $this->status,
            'attendees_count' => $this->attendees_count,
            'special_requirements' => $this->special_requirements,
            'total_price' => $this->total_price,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Relaciones
            'user' => new UserResource($this->whenLoaded('user')),
            'space' => new SpaceResource($this->whenLoaded('space')),
        ];
    }
}
