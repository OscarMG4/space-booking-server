<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'space_id' => $this->space_id,
            'booking_id' => $this->booking_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'is_approved' => (bool) $this->is_approved,
            'is_flagged' => (bool) $this->is_flagged,
            'admin_notes' => $this->admin_notes,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'user' => new UserResource($this->whenLoaded('user')),
            'space' => new SpaceResource($this->whenLoaded('space')),
        ];
    }
}
