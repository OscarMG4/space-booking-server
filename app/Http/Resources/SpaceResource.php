<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'capacity' => $this->capacity,
            'price_per_hour' => $this->price_per_hour,
            'location' => $this->location,
            'floor' => $this->floor,
            'amenities' => $this->amenities,
            'image_url' => $this->image_url,
            'is_available' => $this->is_available,
            'rules' => $this->rules,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            //relaciones opcionales
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'availabilities' => $this->whenLoaded('availabilities'),
        ];
    }
}
